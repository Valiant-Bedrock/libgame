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

namespace libgame;

use libgame\arena\ArenaManager;
use libgame\game\GameManager;
use pocketmine\plugin\PluginBase;

/**
 * This abstract class is the base plugin class for all gamemodes.
 *
 */
abstract class GameBase extends PluginBase {

	protected ArenaManager $arenaManager;
	protected GameManager $gameManager;

	protected function onLoad(): void {
		$this->arenaManager = $this->setupArenaManager();
		$this->gameManager = new GameManager($this);
	}

	public function getGameManager(): GameManager {
		return $this->gameManager;
	}

	protected abstract function setupArenaManager(): ArenaManager;

	public function getArenaManager(): ArenaManager {
		return $this->arenaManager;
	}

}