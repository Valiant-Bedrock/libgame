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

/**
 * This class is the logic behind each game state and should be setup when overriding the `Game` class.
 */
abstract class GameStateHandler {
	use GameTrait;

	public function __construct(Game $game) {
		$this->setGame($game);
	}

	/**
	 * This method is called when the state is first initialized.
	 */
	public abstract function handleSetup(): void;

	/**
	 * This method is called every game tick.
	 */
	public abstract function handleTick(int $currentStateTime): void;

	/**
	 * This method is called when the state is being replaced by another state or the game is being closed.
	 */
	public abstract function handleFinish(): void;

}