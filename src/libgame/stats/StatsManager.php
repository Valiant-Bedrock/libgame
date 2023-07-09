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

use libgame\GameBase;
use pocketmine\player\Player;

/**
 * @template TStats of PlayerStats
 */
abstract class StatsManager {

	/** @var array<string, TStats> */
	protected array $stats = [];

	public function __construct(public readonly GameBase $plugin) {
	}

	public function getPlugin(): GameBase {
		return $this->plugin;
	}

	/**
	 * @return array<string, TStats>
	 */
	public function getStats(): array {
		return $this->stats;
	}

	/**
	 * @param TStats $stats
	 */
	public function add(Player $player, PlayerStats $stats): void {
		$this->stats[$player->getXuid()] = $stats;
	}

	public function remove(Player $player): void {
		unset($this->stats[$player->getXuid()]);
	}
}