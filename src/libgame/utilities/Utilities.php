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

namespace libgame\utilities;

use pocketmine\utils\TextFormat;
use function array_rand;

class Utilities {

	/** @var string[]  */
	private static array $WHITELISTED_COLORS = [
		TextFormat::DARK_BLUE,
		TextFormat::DARK_GREEN,
		TextFormat::DARK_AQUA,
		TextFormat::DARK_PURPLE,
		TextFormat::GOLD,
		TextFormat::BLUE,
		TextFormat::GREEN,
		TextFormat::AQUA,
		TextFormat::RED,
		TextFormat::LIGHT_PURPLE,
		TextFormat::YELLOW,
		TextFormat::MINECOIN_GOLD
	];

	/**
	 * This method pulls a random color from a list of whitelisted colors.
	 * This is primarily used for choosing team colors, but can be used for other purposes.
	 */
	public static function getRandomColor(): string {
		return self::$WHITELISTED_COLORS[array_rand(self::$WHITELISTED_COLORS)];
	}

}