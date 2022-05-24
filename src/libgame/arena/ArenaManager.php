<?php
/**
 *
 * Copyright (C) 2020 - 2022 | Matthew Jordan
 *
 * This program is private software. You may not redistribute this software, or
 * any derivative works of this software, in source or binary form, without
 * the express permission of the owner.
 *
 * @author sylvrs
 */
declare(strict_types=1);

namespace libgame\arena;

class ArenaManager {

	/** @var array<Arena> */
	protected array $arenas = [];

	public function __construct() {}


	public function add(Arena $arena): void {
		$this->arenas[spl_object_id($arena)] = $arena;
	}

	public function remove(Arena $arena): void {
		unset($this->arenas[spl_object_id($arena)]);
	}

	/**
	 * @return array<Arena>
	 */
	public function getAll(): array {
		return $this->arenas;
	}

	public function getOpenArena(): ?Arena {
		foreach ($this->arenas as $arena) {
			if (!$arena->isOccupied()) {
				return $arena;
			}
		}
		return null;
	}

}