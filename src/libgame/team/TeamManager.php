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
use libgame\game\GameTrait;
use libgame\team\member\MemberState;
use libgame\utilities\Utilities;
use pocketmine\player\Player;
use pocketmine\utils\AssumptionFailedError;
use function array_filter;
use function array_merge;

class TeamManager
{
	use GameTrait;

	protected int $teamCounter = 0;
	/** @var array<Team> */
	protected array $teams = [];
	/** @var array<TeamState> */
	protected array $states = [];

	public function __construct(Game $game, protected TeamMode $mode) {
		$this->setGame($game);
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

	public function getTeamNullable(Player $player): ?Team {
		foreach ($this->getAll() as $team) {
			if ($team->isMember($player)) {
				return $team;
			}
		}
		return null;
	}

	public function getTeam(Player $player): Team {
		return $this->getTeamNullable($player) ?? throw new AssumptionFailedError("Player is not in a team");
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
				if ($this->getPlayerState($member) === MemberState::ALIVE()) {
					$players[$member->getUniqueId()->toString()] = $member;
				}
			}
		}
		return $players;
	}

	public function getTeamState(Team $team): TeamState {
		return $this->states[$team->getId()];
	}

	public function getPlayerState(Player $player): ?MemberState {
		$team = $this->getTeamNullable($player);
		if ($team === null) {
			return null;
		}
		return $this->getTeamState($team)->getState($player);
	}

	public function setPlayerState(Player $player, MemberState $state): void {
		$team = $this->getTeamNullable($player);
		if ($team === null) {
			return;
		}
		$this->states[$team->getId()]->setState($player, $state);
	}

	public function removePlayerFromTeam(Player $player): void {
		if ($this->hasTeam($player)) {
			$team = $this->getTeam($player);
			$team->removeMember($player);
			$this->getTeamState($team)->removeState($player);
		}
	}

	public function removePlayerByUUID(string $uuid): void {
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
	 * This method will generate team information like an ID and a name.
	 *
	 * @return array{int, string} - Returns an array with the shape {id: int, color: string}
	 */
	public function generateTeamData(): array {
		return [
			++$this->teamCounter,
			Utilities::getRandomColor(),
		];
	}

	/**
	 * This method checks if two players are on the same team.
	 */
	public function checkOnTeams(Player $firstPlayer, Player $secondPlayer): bool {
		return $this->hasTeam($firstPlayer) && $this->hasTeam($secondPlayer) && $this->getTeam($firstPlayer) === $this->getTeam($secondPlayer);
	}
}