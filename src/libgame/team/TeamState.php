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

use InvalidArgumentException;
use libgame\team\member\MemberState;
use pocketmine\player\Player;
use function array_fill_keys;
use function array_keys;

class TeamState {

	/**
	 * @param array<MemberState> $memberStates
	 */
	public function __construct(protected int $teamId, protected array $memberStates) {}

	public static function create(Team $team): TeamState {
		return new TeamState(
			teamId: $team->getId(),
			memberStates: array_fill_keys(
				keys: array_keys($team->getMembers()),
				value: MemberState::ALIVE()
			)
		);
	}

	/**
	 * This method gets the state of a player in the team.
	 */
	public function getState(Player $player): ?MemberState {
		return $this->memberStates[$player->getUniqueId()->getBytes()] ?? null;

	}

	/**
	 * This method sets the state of a player in the team.
	 */
	public function setState(Player $player, MemberState $state): void {
		$this->setStateByUUID($player->getUniqueId()->getBytes(), $state);
	}

	public function setStateByUUID(string $uuid, MemberState $state): void {
		$this->memberStates[$uuid] = $state;
	}

	public function removeState(Player $player): void {
		$this->removeStateByUUID($player->getUniqueId()->getBytes());
	}

	public function removeStateByUUID(string $uuid): void {
		if (isset($this->memberStates[$uuid])) {
			unset($this->memberStates[$uuid]);
		}
	}

	public function isAlive(): bool {
		foreach ($this->memberStates as $state) {
			if ($state->equals(MemberState::ALIVE())) {
				return true;
			}
		}
		return false;
	}

}