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

namespace libgame\game\round;

use pocketmine\utils\EnumTrait;

/**
 * @method static RoundState PREROUND()
 * @method static RoundState IN_ROUND()
 * @method static RoundState POSTROUND()
 */
class RoundState {
	use EnumTrait;

	protected static function setup(): void {
		self::register(new RoundState("preround"));
		self::register(new RoundState("in_round"));
		self::register(new RoundState("postround"));
	}
}