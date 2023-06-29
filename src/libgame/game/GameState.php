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

enum GameState {
	case WAITING;
	case STARTING;
	case IN_GAME;
	case POSTGAME;

	public function getNextState(): ?GameState {
		return match ($this) {
			self::WAITING => self::STARTING,
			self::STARTING => self::IN_GAME,
			self::IN_GAME => self::POSTGAME,
			default => null
		};
	}

}