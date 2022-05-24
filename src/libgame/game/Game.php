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

namespace libgame\game;

use libgame\Arena;
use libgame\event\GameStateChangeEvent;
use libgame\scoreboard\ScoreboardManager;
use libgame\team\TeamManager;
use libgame\team\TeamMode;

class Game {

	protected GameState $state;

	protected ScoreboardManager $scoreboardManager;
	protected TeamManager $teamManager;

	/**
	 * @param string $uniqueId - A unique identifier to distinguish this game from others.
	 * @param Arena $arena - An arena to play in
	 * @param TeamMode $teamMode - The type of mode to use for the game (Solo, Duos, etc.)
	 * @param string $title - The title of the scoreboard to display
	 */
	public function __construct(
		protected string $uniqueId,
		protected Arena $arena,
		protected TeamMode $teamMode,
		protected string $title
	)
	{
		$this->state = GameState::WAITING();

		$this->scoreboardManager = new ScoreboardManager(game: $this, title: $title);
		$this->teamManager = new TeamManager(game: $this, mode: $teamMode);
	}

	public function getUniqueId(): string {
		return $this->uniqueId;
	}

	public function getArena(): Arena {
		return $this->arena;
	}

	public function getTeamMode(): TeamMode {
		return $this->teamMode;
	}

	public function getState(): GameState {
		return $this->state;
	}

	/**
	 * @param GameState $state
	 */
	public function setState(GameState $state): void {
		$event = new GameStateChangeEvent(
			game: $this,
			oldState: $this->state,
			newState: $state
		);
		$event->call();

		$this->state = $state;
	}

	public function getTeamManager(): TeamManager {
		return $this->teamManager;
	}
}