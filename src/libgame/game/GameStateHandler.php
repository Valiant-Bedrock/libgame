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

namespace libgame\game;

/**
 * This class is the logic behind each game state and should be setup when overriding the `Game` class.
 *
 * @template T of Game
 */
abstract class GameStateHandler {

	/**
	 * @param T $game
	 */
	public function __construct(protected Game $game) {}

	/**
	 * @return T
	 */
	public function getGame(): Game {
		return $this->game;
	}

	/**
	 * @param T $game
	 */
	public function setGame(Game $game): void {
		$this->game = $game;
	}

	/**
	 * This method is called when the state is first initialized.
	 *
	 * @return void
	 */
	public abstract function handleSetup(): void;

	/**
	 * This method is called every game tick.
	 *
	 * @param int $currentStateTime
	 * @return void
	 */
	public abstract function handleTick(int $currentStateTime): void;

	/**
	 * This method is called when the state is being replaced by another state or the game is being closed.
	 *
	 * @return void
	 */
	public abstract function handleFinish(): void;

}