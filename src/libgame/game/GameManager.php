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

use libgame\GameBase;
use libgame\utilities\GameBaseTrait;

class GameManager {
	use GameBaseTrait;

	/** @var array<Game> */
	protected array $games = [];

	public function __construct(GameBase $plugin) {
		$this->setPlugin($plugin);
	}

	/**
	 * Adds a game to the manager.
	 *
	 * @param Game $game
	 * @return void
	 */
	public function add(Game $game): void {
		$this->games[$game->getUniqueId()] = $game;
	}

	/**
	 * Removes a game from the manager
	 *
	 * @param Game $game
	 * @return void
	 */
	public function remove(Game $game): void {
		unset($this->games[$game->getUniqueId()]);
	}

	/**
	 * Attempts to get a game by its unique id.
	 *
	 * @param string $uniqueId
	 * @return Game|null
	 */
	public function get(string $uniqueId) : ?Game {
		return $this->games[$uniqueId] ?? null;
	}

	/**
	 * @return array<Game>
	 */
	public function getAll(): array {
		return $this->games;
	}

}