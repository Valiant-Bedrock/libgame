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

use Closure;
use libgame\arena\Arena;
use libgame\event\GameStateChangeEvent;
use libgame\GameBase;
use libgame\handler\EventHandler;
use libgame\handler\GameEventHandler;
use libgame\ScoreboardManager;
use libgame\SpectatorManager;
use libgame\team\Team;
use libgame\team\TeamManager;
use libgame\team\TeamMode;
use libgame\utilities\DeployableClosure;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\Sound;
use PrefixedLogger;
use function array_filter;
use function array_keys;
use function array_map;

abstract class Game {

	protected PrefixedLogger $logger;

	protected DeployableClosure $heartbeat;

	protected GameState $state = GameState::WAITING;
	/** @var array<key-of<GameState>, GameStateHandler> */
	protected array $stateHandlers = [];

	/** @var array<key-of<GameState>, EventHandler[]> */
	protected array $stateEventHandlers = [];
	protected int $currentStateTime = 0;

	protected ScoreboardManager $scoreboardManager;
	protected SpectatorManager $spectatorManager;

	/**
	 *  A list of players that are unassociated with a team. This is used only before a game starts.
	 *
	 * @var array<string, bool>
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
		protected readonly GameBase $plugin,
		protected readonly string $uniqueId,
		protected Arena $arena,
		protected TeamMode $teamMode,
		protected string $title,
		protected GameEventHandler $eventHandler,
		protected int $heartbeatPeriod = 20
	)
	{
		$this->logger = new PrefixedLogger(delegate: $plugin->getLogger(), prefix: "Game $this->uniqueId");
		$this->heartbeat = new DeployableClosure($this->tick(...), $plugin->getScheduler());
		// Setups the state handlers
		$this->setStateHandler(GameState::WAITING, $this->setupWaitingStateHandler(...));
		$this->setStateHandler(GameState::STARTING, $this->setupStartingStateHandler(...));
		$this->setStateHandler(GameState::IN_GAME, $this->setupInGameStateHandler(...));
		$this->setStateHandler(GameState::POSTGAME, $this->setupPostGameStateHandler(...));
		// Sets the initial state
		$this->setState(GameState::WAITING);

		$this->scoreboardManager = new ScoreboardManager(title: $title);
		$this->spectatorManager = new SpectatorManager(game: $this);

		$this->teamManager = new TeamManager(game: $this, mode: $teamMode);

		$this->heartbeat->deploy(period: $this->heartbeatPeriod);
	}

	public function getLogger(): PrefixedLogger {
		return $this->logger;
	}

	/**
	 * Returns a prefix that can supersede message broadcasts.
	 */
	public function getPrefix(): string {
		return TextFormat::MINECOIN_GOLD . "Game > ";
	}

	public function getPlugin(): GameBase {
		return $this->plugin;
	}

	/**
	 * A simple getter to reduce extraneous call chaining (e.g., $this->getPlugin()->getServer()->getX()).
	 */
	public function getServer(): Server {
		return $this->plugin->getServer();
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

		// Unregister old event handlers
		$currentEventHandlers = $this->getEventHandlers($this->state);
		foreach ($currentEventHandlers as $eventHandler) {
			// don't worry about unregistering handlers that aren't registered
			if (!$eventHandler->isRegistered()) {
				continue;
			}
			$eventHandler->unregister();
		}

		$this->state = $state;
		// Grabs the new state handler and sets it up
		$newStateHandler = $this->getStateHandler($state);
		$newStateHandler->handleSetup();
		// Setup new event handlers
		foreach ($this->getEventHandlers($state) as $eventHandler) {
			$eventHandler->register($this->plugin);
		}
		$this->currentStateTime = 0;
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
	 */
	public function tick(): void {
		$this->scoreboardManager->update();
		$this->getCurrentStateHandler()->handleTick($this->currentStateTime);
		$this->currentStateTime++;
	}

	/**
	 * @param Closure(Game): GameStateHandler $stateHandler
	 */
	private function setStateHandler(GameState $state, Closure $stateHandler): void {
		$this->stateHandlers[$state->name] = $stateHandler($this);
	}

	/**
	 * Returns the state handler for the passed state.
	 */
	protected function getStateHandler(GameState $state): GameStateHandler {
		return $this->stateHandlers[$state->name] ?? throw new AssumptionFailedError("No handler for state $state->name");
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
	 * This abstract method is the game state handler behind the game state {@link GameState::WAITING}.
	 */
	public abstract function setupWaitingStateHandler(Game $game): GameStateHandler;

	/**
	 * This abstract method is the game state handler behind the game state {@link GameState::STARTING}.
	 */
	public abstract function setupStartingStateHandler(Game $game): GameStateHandler;

	/**
	 * This abstract method is the game state handler behind the game state {@link GameState::IN_GAME}.
	 */
	public abstract function setupInGameStateHandler(Game $game): GameStateHandler;

	/**
	 * This abstract method is the game state handler behind the game state {@link GameState::POSTGAME}.
	 */
	public abstract function setupPostGameStateHandler(Game $game): GameStateHandler;

	public function registerEventHandler(GameState $state, EventHandler $handler): void {
		$this->stateEventHandlers[$state->name] ??= [];
		$this->stateEventHandlers[$state->name][] = $handler;
	}

	/**
	 * @return EventHandler[]
	 */
	private function getEventHandlers(GameState $state): array {
		return $this->stateEventHandlers[$state->name] ?? [];
	}

	public function isUnassociatedPlayer(Player $player): bool {
		return isset($this->unassociatedPlayers[$player->getUniqueId()->getBytes()]);
	}

	public function addUnassociatedPlayer(Player $player): void {
		$this->unassociatedPlayers[$player->getUniqueId()->getBytes()] = true;
	}

	public function removeUnassociatedPlayer(Player $player): void {
		unset($this->unassociatedPlayers[$player->getUniqueId()->getBytes()]);
	}

	/**
	 * @return array<Player>
	 */
	public function getUnassociatedPlayers(): array {
		return array_filter(
			array_map(
				callback: fn(string $uniqueId) => $this->getServer()->getPlayerByRawUUID($uniqueId),
				array: array_keys($this->unassociatedPlayers)
			)
		);
	}

	/**
	 * This method returns true if the player is involved in the game in any manner.
	 */
	public function isInGame(Player $player): bool {
		return $this->getSpectatorManager()->isSpectator($player) || $this->getTeamManager()->hasTeam($player) || $this->isUnassociatedPlayer($player);
	}

	/**
	 * This method is called when a player requests to join the game.
	 */
	public abstract function handleJoin(Player $player): void;

	/**
	 * This method is called whenever a player requests to leave a game / quits the server.
	 */
	public abstract function handleQuit(Player $player): void;

	/**
	 * @param Closure(Player): void $closure
	 */
	public function executeOnAll(Closure $closure): void {
		$all = array_filter(array: $this->getServer()->getOnlinePlayers(), callback: fn(Player $player): bool => $player->isConnected() && $this->isInGame($player));
		foreach ($all as $player) {
			$closure($player);
		}
	}

	/**
	 * @param Closure(Team): void $closure
	 */
	public function executeOnTeams(Closure $closure): void {
		foreach ($this->getTeamManager()->getAll() as $team) {
			$closure($team);
		}
	}

	/**
	 * @param Closure(Player): void $closure
	 */
	public function executeOnPlayers(Closure $closure): void {
		foreach ($this->getTeamManager()->getAll() as $team) {
			$team->executeOnPlayers($closure);
		}
	}

	/**
	 * @param Closure(Player): void $closure
	 */
	public function executeOnSpectators(Closure $closure): void {
		foreach ($this->getSpectatorManager()->getAll() as $player) {
			$closure($player);
		}
	}

	/**
	 * Broadcasts a message to all players in the game.
	 */
	public function broadcastMessage(string $message, bool $prependPrefix = true): void {
		if ($prependPrefix) $message = $this->getPrefix() . TextFormat::RESET . TextFormat::WHITE . $message;
		$this->executeOnAll(function (Player $player) use ($message): void {
			$player->sendMessage($message);
		});
	}

	/**
	 * Broadcasts a tip to all players in the game.
	 */
	public function broadcastTip(string $tip): void {
		$this->executeOnAll(function (Player $player) use ($tip): void {
			$player->sendTip($tip);
		});
	}

	/**
	 * Broadcasts a tip to all players in the game.
	 */
	public function broadcastPopup(string $popup): void {
		$this->executeOnAll(function (Player $player) use ($popup): void {
			$player->sendPopup($popup);
		});
	}

	/**
	 * Broadcasts a sound to all players in the game
	 */
	public function broadcastSound(Sound $sound): void {
		$this->executeOnAll(function (Player $player) use ($sound): void {
			$player->getWorld()->addSound($player->getPosition(), $sound, [$player]);
		});
	}

	/**
	 * This method is called when a game is over and needs to be cleaned up.
	 */
	public function finish(): void {
		$this->handleFinish();
		$this->teamManager->finish();
		$this->unassociatedPlayers = [];
		$this->spectatorManager->finish();
		$this->scoreboardManager->finish();
		$this->heartbeat->cancel();
		// clean up event handlers
		if ($this->eventHandler->isRegistered()) {
			$this->eventHandler->unregister();
		}
		foreach ($this->stateEventHandlers as $handlers) {
			foreach ($handlers as $handler) {
				if ($handler->isRegistered()) {
					$handler->unregister();
				}
			}
		}
	}

	protected abstract function handleFinish(): void;

}