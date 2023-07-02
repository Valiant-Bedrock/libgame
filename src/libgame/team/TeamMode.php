<?php
/**
 * Copyright (C) 2020 - 2022 | Matthew Jordan
 *
 * This program is private software. You may not redistribute this software, or
 * any derivative works of this software, in source or binary form, without
 * the express permission of the owner.
 *
 * @author sylvrs
 */
declare(strict_types=1);

namespace libgame\team;

use InvalidArgumentException;
use pocketmine\utils\EnumTrait;
use function strtolower;

/**
 * TeamMode is an enum class that represents available team modes (1, 2, 3, 4, 5, 10)
 *
 * @method static TeamMode SOLO()
 * @method static TeamMode DUOS()
 * @method static TeamMode TRIOS()
 * @method static TeamMode SQUADS()
 * @method static TeamMode FIVE_MAN()
 * @method static TeamMode ULTRA_SQUADS()
 */
class TeamMode {
	use EnumTrait {
		__construct as private EnumTrait__construct;
	}

	protected static function setup(): void {
		self::registerAll(
			new TeamMode("solo", "Solo", 1),
			new TeamMode("duos", "Duos",2),
			new TeamMode("trios", "Trios", 3),
			new TeamMode("squads", "Squads", 4),
			new TeamMode("five_man", "Five Man", 5),
			new TeamMode("ultra_squads", "Ultra Squads", 10)
		);
	}

	/**
	 * This static method is used to create a custom team mode
	 */
	public static function custom(string $name, string $formattedName, int $maxPlayerCount): TeamMode {
		return new TeamMode($name, $formattedName, $maxPlayerCount);
	}

	/** @var array<string, TeamMode> */
	protected static array $formattedNameMappings = [];

	public function __construct(
		string $name,
		protected string $formattedName,
		protected int $maxPlayerCount
	) {
		$this->EnumTrait__construct($name);
		self::$formattedNameMappings[strtolower($formattedName)] = $this;
	}

	public function getFormattedName(): string {
		return $this->formattedName;
	}

	public function getMaxPlayerCount(): int {
		return $this->maxPlayerCount;
	}

	public static function fromString(string $name): TeamMode {
		try {
			/** @var TeamMode $mode */
			$mode = self::_registryFromString($name);
			return $mode;
		} catch (InvalidArgumentException) {
			return self::$formattedNameMappings[strtolower($name)] ?? throw new InvalidArgumentException("Invalid team mode $name");
		}
	}
}