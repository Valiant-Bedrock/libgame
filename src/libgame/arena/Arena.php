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

use pocketmine\world\World;

class Arena {

	protected bool $occupied = false;

	public function __construct(protected World $world) {}

	public function getWorld(): World {
		return $this->world;
	}

	public function isOccupied(): bool {
		return $this->occupied;
	}

	public function setOccupied(bool $occupied): void {
		$this->occupied = $occupied;
	}

}