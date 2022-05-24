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

use Closure;
use libgame\Arena;
use libgame\event\GameStateChangeEvent;
use libgame\GameBase;
use libgame\scoreboard\ScoreboardManager;
use libgame\spectator\SpectatorManager;
use libgame\team\TeamManager;
use libgame\team\TeamMode;
use libgame\utilities\DeployableClosure;
use libgame\utilities\GameBaseTrait;
use pocketmine\Server;
use pocketmine\utils\AssumptionFailedError;

abstract class Game {
	use GameBaseTrait;

	protected DeployableClosure $heartbeat;

	protected GameState $state;
	/** @var array<GameStateHandler> */
	protected array $stateHandlers = [];
	protected int $currentStateTime = 0;

	protected ScoreboardManager $scoreboardManager;

	protected SpectatorManager $spectatorManager;

	/**
	 *  A list of players that are unassociated with a team. This is used only before a game starts.
	 *
	 * @var array<string>
	 */
	protected array $unassociatedPlayers = [];
	protected TeamManager $teamManager;

	/**
	 * @param GameBase $plugin - The plugin instance associated with the game.
	 * @param string $uniqueId - A unique identifier to distinguish this game from others.
	 * @param Arena $arena - An arena to play in
	 * @param TeamMode $teamMode - The type of mode to use for the game (Solo, Duos, etc.)
	 * @param string $title - The title of the scoreboard to display
	 * @param int $heartbeatPeriod - The number of ticks between each heartbeat
	 */
	public function __construct(
		GameBase $plugin,
		protected string $uniqueId,
		protected Arena $arena,
		protected TeamMode $teamMode,
		protected string $title,
		protected int $heartbeatPeriod = 20
	)
	{
		$this->setPlugin($plugin);
		$this->heartbeat = new DeployableClosure(Closure::fromCallable([$this, "tick"]), $plugin->getScheduler());
		// Setups the state handlers
		$this->stateHandlers = [
			GameState::WAITING()->id() => $this->setupWaitingStateHandler($this),
			GameState::COUNTDOWN()->id() => $this->setupCountdownStateHandler($this),
			GameState::IN_GAME()->id() => $this->setupInGameStateHandler($this),
			GameState::POSTGAME()->id() => $this->setupPostGameStateHandler($this),
		];
		// Sets the initial state
		$this->state = GameState::WAITING();
		$this->setState(GameState::WAITING());

		$this->scoreboardManager = new ScoreboardManager(game: $this, title: $title);
		$this->spectatorManager = new SpectatorManager(game: $this);

		$this->teamManager = new TeamManager(game: $this, mode: $teamMode);


		$this->heartbeat->deploy(period: $this->heartbeatPeriod);
	}

	/**
	 * This method is a simple getter to reduce extraneous call chaining (e.g., $this->getPlugin()->getServer()->getX()).
	 *
	 * @return Server
	 */
	public function getServer(): Server {
		return $this->getPlugin()->getServer();
	}

	public function getUniqueId(): string {
		return $this->uniqueId;
	}

	public function getArena(): Arena {
		return $this->arena;
	}

	public function getTeamMode(): TeamMode {
		return $this->teamMode;
	}

	public function getHeartbeat(): DeployableClosure {
		return $this->heartbeat;
	}

	public function getState(): GameState {
		return $this->state;
	}

	/**
	 * @param GameState $state
	 */
	public function setState(GameState $state): void {
		$event = new GameStateChangeEvent(
			game: $this,
			oldState: $this->state,
			newState: $state
		);
		$event->call();

		// Grabs the current state handler and finishes it.
		$currentStateHandler = $this->getStateHandler($this->state);
		$currentStateHandler->handleFinish();

		// Grabs the new state handler and sets it up
		$newStateHandler = $this->getStateHandler($state);
		$newStateHandler->handleSetup();
		$this->state = $state;
	}

	public function getScoreboardManager(): ScoreboardManager {
		return $this->scoreboardManager;
	}

	public function getSpectatorManager(): SpectatorManager {
		return $this->spectatorManager;
	}

	public function getTeamManager(): TeamManager {
		return $this->teamManager;
	}

	/**
	 * This method is the main update loop for the game.
	 *
	 * @return void
	 */
	public function tick(): void {
		$this->scoreboardManager->update();
		$this->getCurrentStateHandler()->handleTick($this->currentStateTime);
	}

	/**
	 * Returns the state handler for the passed state.
	 *
	 * @param GameState $state
	 * @return GameStateHandler
	 */
	protected function getStateHandler(GameState $state): GameStateHandler {
		return $this->stateHandlers[$state->id()] ?? throw new AssumptionFailedError("No handler for state {$state->name()}");
	}

	protected function getCurrentStateHandler(): GameStateHandler {
		return $this->getStateHandler($this->state);
	}

	/**
	 * @return array<GameStateHandler>
	 */
	protected function getStateHandlers(): array {
		return $this->stateHandlers;
	}

	/**
	 * This abstract method is the game state handler behind the game state {@link GameState::WAITING()}.
	 *
	 * @param Game $game
	 * @return GameStateHandler
	 */
	public abstract function setupWaitingStateHandler(Game $game): GameStateHandler;

	/**
	 * This abstract method is the game state handler behind the game state {@link GameState::COUNTDOWN()}.
	 *
	 * @param Game $game
	 * @return GameStateHandler
	 */
	public abstract function setupCountdownStateHandler(Game $game): GameStateHandler;

	/**
	 * This abstract method is the game state handler behind the game state {@link GameState::IN_GAME()}.
	 *
	 * @param Game $game
	 * @return GameStateHandler
	 */
	public abstract function setupInGameStateHandler(Game $game): GameStateHandler;

	/**
	 * This abstract method is the game state handler behind the game state {@link GameState::POSTGAME()}.
	 *
	 * @param Game $game
	 * @return GameStateHandler
	 */
	public abstract function setupPostGameStateHandler(Game $game): GameStateHandler;

}