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
		return $this->memberStates[$player->getId()] ?? null;

	}

	/**
	 * This method sets the state of a player in the team.
	 */
	public function setState(Player|string $player, MemberState $state): void {
		$uuid = $player instanceof Player ? $player->getUniqueId()->getBytes() : $player;

		if (!isset($this->memberStates[$uuid])) {
			throw new InvalidArgumentException("UUID $uuid is not in team $this->teamId");
		}
		$this->memberStates[$uuid] = $state;
	}

	public function isAlive(): bool {
		foreach ($this->memberStates as $state) {
			if ($state->equals(MemberState::DEAD())) {
				return false;
			}
		}
		return true;
	}

}