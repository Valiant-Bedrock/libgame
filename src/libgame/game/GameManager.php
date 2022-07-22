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

namespace libgame\game;

use libgame\GameBase;
use libgame\utilities\GameBaseTrait;
use pocketmine\player\Player;
use pocketmine\world\World;
use function array_filter;

class GameManager {
	use GameBaseTrait;

	/** @var array<string, Game> */
	protected array $games = [];

	public function __construct(GameBase $plugin) {
		$this->setPlugin($plugin);
	}

	/**
	 * Adds a game to the manager.
	 */
	public function add(Game $game): void {
		$this->games[$game->getUniqueId()] = $game;
	}

	/**
	 * Removes a game from the manager
	 */
	public function remove(Game $game): void {
		unset($this->games[$game->getUniqueId()]);
	}

	/**
	 * Attempts to get a game by its unique id.
	 */
	public function get(string $uniqueId) : ?Game {
		return $this->games[$uniqueId] ?? null;
	}

	/**
	 * @return array<string, Game>
	 */
	public function getAll(): array {
		return $this->games;
	}

	/**
	 * @return array<Game>
	 */
	public function getFreeGames(): array {
		return array_filter(
			array: $this->getAll(),
			callback: fn(Game $game) => $game->getState()->equals(GameState::WAITING())
		);
	}

	/**
	 * Attempts to get the game by the player.
	 */
	public function getGameByPlayer(Player $player): ?Game {
		foreach($this->getAll() as $game) {
			if($game->isInGame($player)) {
				return $game;
			}
		}
		return null;
	}

	/**
	 * Attempts to get the game by the world it is in.
	 */
	public function getGameByWorld(World $world): ?Game {
		foreach($this->getAll() as $game) {
			if($game->getArena()->getWorld() === $world) {
				return $game;
			}
		}
		return null;
	}

}