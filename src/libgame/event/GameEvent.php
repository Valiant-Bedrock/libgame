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

namespace libgame\event;

use libgame\game\Game;
use pocketmine\event\Event;

abstract class GameEvent extends Event {

	public function __construct(protected readonly Game $game) {
	}

	public function getGame(): Game {
		return $this->game;
	}
}