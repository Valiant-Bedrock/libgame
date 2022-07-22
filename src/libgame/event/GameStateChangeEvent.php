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
use libgame\game\GameState;

class GameStateChangeEvent extends GameEvent {

	public function __construct(Game $game, protected GameState $oldState, protected GameState $newState) {
		parent::__construct($game);
	}

	public function getOldState(): GameState {
		return $this->oldState;
	}

	public function getNewState(): GameState {
		return $this->newState;
	}

}