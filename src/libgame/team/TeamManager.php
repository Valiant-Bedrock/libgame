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

namespace libgame\team;

use libgame\game\Game;
use libgame\team\member\MemberState;
use libgame\utilities\Utilities;
use pocketmine\player\Player;
use pocketmine\utils\AssumptionFailedError;
use Ramsey\Uuid\UuidInterface;
use function array_filter;
use function array_merge;

class TeamManager {
	protected int $teamCounter = 0;
	/** @var array<int, Team> */
	protected array $teams = [];
	/** @var array<int, TeamState> */
	protected array $states = [];

	public function __construct(
		public readonly Game $game,
		protected TeamMode $mode
	) {
	}

	public function getGame(): Game {
		return $this->game;
	}

	/**
	 * This method will return a team given its ID or null if the team isn't found.
	 */
	public function get(int $id): ?Team {
		return $this->teams[$id] ?? null;
	}

	public function add(Team $team): void {
		$this->teams[$team->getId()] = $team;
		$this->states[$team->getId()] = TeamState::create($team);
	}

	public function remove(Team $team): void
	{
		unset($this->teams[$team->getId()]);
		unset($this->states[$team->getId()]);
	}

	/**
	 * @return array<int, Team>
	 */
	public function getAll(): array {
		return $this->teams;
	}

	public function getStartingCount(): int {
		return $this->teamCounter;
	}

	/**
	 * @return array<Player>
	 */
	public function getOnlinePlayers(): array {
		$players = [];
		foreach ($this->getAll() as $team) {
			$players = array_merge($players, $team->getOnlineMembers());
		}
		return $players;
	}

	public function hasTeam(Player $player): bool {
		foreach ($this->getAll() as $team) {
			if ($team->isMember($player)) {
				return true;
			}
		}
		return false;
	}

	public function hasTeamByUUID(UuidInterface $uuid): bool {
		foreach ($this->getAll() as $team) {
			if ($team->isMemberByUUID($uuid)) {
				return true;
			}
		}
		return false;
	}

	public function getTeamNullable(Player $player): ?Team {
		foreach ($this->getAll() as $team) {
			if ($team->isMember($player)) {
				return $team;
			}
		}
		return null;
	}

	public function getTeamByUUIDNullable(UuidInterface $uuid): ?Team {
		foreach ($this->getAll() as $team) {
			if ($team->isMemberByUUID($uuid)) {
				return $team;
			}
		}
		return null;
	}

	public function getTeam(Player $player): Team {
		return $this->getTeamNullable($player) ?? throw new AssumptionFailedError("Player is not in a team");
	}

	public function getTeamByUUID(UuidInterface $uuid): Team {
		return $this->getTeamByUUIDNullable($uuid) ?? throw new AssumptionFailedError("Player is not in a team");
	}

	/**
	 * @return array<Team>
	 */
	public function getAliveTeams(): array {
		return array_filter(
			array: $this->getAll(),
			callback: fn(Team $team) => $this->getTeamState($team)->isAlive()
		);
	}

	/**
	 * @return array<Player>
	 */
	public function getAlivePlayers(): array {
		$players = [];
		foreach ($this->getAliveTeams() as $team) {
			foreach ($team->getOnlineMembers() as $member) {
				if ($this->getPlayerState($member) === MemberState::ALIVE) {
					$players[$member->getUniqueId()->toString()] = $member;
				}
			}
		}
		return $players;
	}

	public function getTeamState(Team $team): TeamState {
		return $this->states[$team->getId()] ?? throw new AssumptionFailedError("Team state not found");
	}

	public function getPlayerState(Player $player): ?MemberState {
		$team = $this->getTeamNullable($player);
		if ($team === null) {
			return null;
		}
		return $this->getTeamState($team)->getState($player);
	}

	public function getPlayerStateByUUID(UuidInterface $uuid): ?MemberState {
		$team = $this->getTeamByUUIDNullable($uuid);
		if ($team === null) {
			return null;
		}
		return $this->getTeamState($team)->getStateByUUID($uuid);
	}

	public function isAlive(Player $player): bool {
		$team = $this->getTeamNullable($player);
		if ($team === null) {
			return false;
		}
		return $this->getTeamState($team)->getState($player) === MemberState::ALIVE;
	}

	public function setPlayerState(Player $player, MemberState $state): void {
		$team = $this->getTeamNullable($player);
		if ($team === null) {
			return;
		}
		$this->states[$team->getId()]->setState($player, $state);
	}

	public function setPlayerStateByUUID(UuidInterface $uuid, MemberState $state): void {
		$team = $this->getTeamByUUIDNullable($uuid);
		if ($team === null) {
			return;
		}
		$this->states[$team->getId()]->setStateByUUID($uuid, $state);
	}

	public function removePlayerFromTeam(Player $player): void {
		if ($this->hasTeam($player)) {
			$team = $this->getTeam($player);
			$team->removeMember($player);
			$this->getTeamState($team)->removeState($player);
		}
	}

	public function removePlayerByUUID(UuidInterface $uuid): void {
		foreach ($this->getAll() as $team) {
			if ($team->isMemberByUUID($uuid)) {
				$team->removeMemberByUUID($uuid);
				$this->getTeamState($team)->removeStateByUUID($uuid);
			}
		}
	}

	public function removePlayerByName(string $username): void {
		foreach ($this->getAll() as $team) {
			if (($data = $team->getMemberDataByName($username)) !== null) {
				$team->removeMemberByUsername($username);
				$this->getTeamState($team)->removeStateByUUID($data->uuid);
			}
		}
	}

	/**
	 * Generates a new team ID and a random color.
	 * @return array{id: int, color: string}
	 */
	public function generateTeamData(): array {
		return [
			"id" => ++$this->teamCounter,
			"color" => Utilities::getRandomColor(),
		];
	}

	/**
	 * This method checks if two players are on the same team.
	 */
	public function checkOnTeams(Player $firstPlayer, Player $secondPlayer): bool {
		return $this->hasTeam($firstPlayer) && $this->hasTeam($secondPlayer) && $this->getTeam($firstPlayer) === $this->getTeam($secondPlayer);
	}

	public function finish(): void {
		$this->teams = [];
		$this->states = [];
		$this->teamCounter = 0;
	}
}