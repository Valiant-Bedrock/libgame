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

namespace libgame\utilities;

use libgame\GameBase;

trait GameBaseTrait {

	protected GameBase $plugin;

	public function getPlugin(): GameBase {
		return $this->plugin;
	}

	public function setPlugin(GameBase $plugin): void {
		$this->plugin = $plugin;
	}

}