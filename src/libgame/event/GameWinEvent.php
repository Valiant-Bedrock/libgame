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
use libgame\team\Team;

class GameWinEvent extends GameEvent {

	public function __construct(Game $game, protected ?Team $winner) {
		parent::__construct($game);
	}

	public function getWinner(): ?Team {
		return $this->winner;
	}

	public function setWinner(?Team $winner): void {
		$this->winner = $winner;
	}

}