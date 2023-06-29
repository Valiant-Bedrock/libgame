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

namespace libgame\spectator;

use libgame\game\Game;
use pocketmine\player\Player;
use function array_filter;
use function array_keys;
use function array_map;

class SpectatorManager {

	/** @var array<string, bool> */
	protected array $spectators = [];

	public function __construct(protected readonly Game $game) {
	}

	public function getGame(): Game {
		return $this->game;
	}

	public function add(Player $player): void {
		$this->spectators[$player->getUniqueId()->getBytes()] = true;
	}

	public function remove(Player $player): void {
		unset($this->spectators[$player->getUniqueId()->getBytes()]);
	}

	public function isSpectator(Player $player): bool {
		return isset($this->spectators[$player->getUniqueId()->getBytes()]);
	}

	/**
	 * @return array<Player>
	 */
	public function getAll(): array {
		return array_filter(
			array: array_map(
				callback: fn(string $uniqueId) => $this->getGame()->getServer()->getPlayerByRawUUID($uniqueId),
				array: array_keys($this->spectators)
			)
		);
	}
}