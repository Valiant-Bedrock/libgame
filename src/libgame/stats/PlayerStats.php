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

use libMarshal\attributes\Field;
use libMarshal\MarshalTrait;

abstract class PlayerStats {
	use MarshalTrait, StatsTrait;

	public function __construct(
		#[Field(name: "xuid")] public readonly string $xuid,
	) {
	}

}