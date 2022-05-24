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

namespace libgame\team\member;

use pocketmine\utils\EnumTrait;

/**
 * @method static MemberState ALIVE()
 * @method static MemberState DEAD()
 */
class MemberState {
	use EnumTrait;

	protected static function setup(): void {
		self::register(new MemberState("ALIVE"));
		self::register(new MemberState("DEAD"));
	}
}