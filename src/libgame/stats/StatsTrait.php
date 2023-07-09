<?php
/**
 * Copyright (C) 2020 - 2023 | Valiant Network
 *
 * This program is private software. You may not redistribute this software, or
 * any derivative works of this software, in source or binary form, without
 * the express permission of the owner.
 *
 * @author sylvrs
 */
declare(strict_types=1);

namespace libgame\stats;

use ReflectionClass;
use function count;

trait StatsTrait {
	/** @var array<string, StatHolder> */
	private static array $statHolders = [];

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array {
		$stats = [];
		foreach (self::getStatHolders() as $holder) {
			$stats[$holder->category] ??= [];
			$stats[$holder->category][$holder->formattedName] = $holder->property->getValue($this);
		}
		return $stats;
	}

	/**
	 * @return array<string, StatHolder>
	 */
	private function getStatHolders(): array {
		return self::$statHolders ??= self::findStats();
	}

	/**
	 * @return array<string, StatHolder>
	 */
	private static function findStats(): array {
		$reflected = new ReflectionClass(self::class);
		$properties = $reflected->getProperties();
		$holders = [];
		foreach ($properties as $property) {
			$attributes = $property->getAttributes(Stat::class);
			if (count($attributes) === 0) {
				continue;
			}
			$stat = $attributes[0]->newInstance();
			$holders[$property->getName()] = new StatHolder($property, $stat->category, $stat->formattedName);
		}
		return $holders;
	}
}