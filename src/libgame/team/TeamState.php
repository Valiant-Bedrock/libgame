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

use libgame\team\member\MemberState;
use pocketmine\player\Player;
use Ramsey\Uuid\UuidInterface;
use function array_fill_keys;
use function array_keys;
use function in_array;

class TeamState {

	/**
	 * @param array<string, MemberState> $memberStates
	 */
	public function __construct(protected int $teamId, protected array $memberStates) {}

	public static function create(Team $team): TeamState {
		return new TeamState(
			teamId: $team->getId(),
			memberStates: array_fill_keys(
				keys: array_keys($team->getMembers()),
				value: MemberState::ALIVE
			)
		);
	}

	/**
	 * This method gets the state of a player in the team.
	 */
	public function getState(Player $player): ?MemberState {
		return $this->getStateByUUID($player->getUniqueId());
	}

	public function getStateByUUID(UuidInterface $uuid): ?MemberState {
		return $this->memberStates[$uuid->getBytes()] ?? null;
	}

	/**
	 * This method sets the state of a player in the team.
	 */
	public function setState(Player $player, MemberState $state): void {
		$this->setStateByUUID($player->getUniqueId(), $state);
	}

	public function setStateByUUID(UuidInterface $uuid, MemberState $state): void {
		$this->memberStates[$uuid->getBytes()] = $state;
	}

	public function removeState(Player $player): void {
		$this->removeStateByUUID($player->getUniqueId());
	}

	public function removeStateByUUID(UuidInterface $uuid): void {
		unset($this->memberStates[$uuid->getBytes()]);
	}

	public function isAlive(): bool {
		return in_array(MemberState::ALIVE, $this->memberStates, true);
	}

}