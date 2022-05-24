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

use libgame\game\GameManager;
use pocketmine\plugin\PluginBase;

class GameBase extends PluginBase {

	protected GameManager $gameManager;

	protected function onLoad(): void {
		$this->gameManager = new GameManager($this);
	}

	public function getGameManager(): GameManager {
		return $this->gameManager;
	}

}