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

use libgame\arena\Arena;
use libgame\game\round\RoundManager;
use libgame\GameBase;
use libgame\handler\GameEventHandler;
use libgame\team\TeamMode;

abstract class RoundBasedGame extends Game {

	public function __construct(
		GameBase $plugin,
		string $uniqueId,
		Arena $arena,
		protected RoundManager $roundManager,
		TeamMode $teamMode,
		string $title,
		GameEventHandler $eventHandler,
		int $heartbeatPeriod = 20
	) {
		parent::__construct($plugin, $uniqueId, $arena, $teamMode, $title, $eventHandler, $heartbeatPeriod);
	}

	public function getRoundManager(): RoundManager {
		return $this->roundManager;
	}

}