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

use pocketmine\utils\EnumTrait;

/**
 * @method static GameState WAITING()
 * @method static GameState STARTING()
 * @method static GameState IN_GAME()
 * @method static GameState POSTGAME()
 */
class GameState {
	use EnumTrait;

	protected static function setup(): void {
		self::register(new GameState("waiting"));
		self::register(new GameState("starting"));
		self::register(new GameState("in_game"));
		self::register(new GameState("postgame"));
	}

	public function getNextState(): ?GameState {
		return match ($this->id()) {
			GameState::WAITING()->id() => GameState::STARTING(),
			GameState::STARTING()->id() => GameState::IN_GAME(),
			GameState::IN_GAME()->id() => GameState::POSTGAME(),
			default => null
		};
	}

}