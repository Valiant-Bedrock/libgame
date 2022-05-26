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

namespace libgame\game\round;

use libgame\game\RoundBasedGame;
use libgame\team\Team;
abstract class RoundManager {

	public const COUNTDOWN_LENGTH = 10;

	/** @var array<Round> */
	protected array $past = [];
	protected Round $current;

	/** @var array<int, int> */
	protected array $scores = [];

	protected int $roundCountdown = self::COUNTDOWN_LENGTH;

	protected RoundState $state;

	public function __construct(protected RoundBasedGame $game) {
		$this->current = new Round(number: 1);
		$this->state = RoundState::PREROUND();
	}

	public function getGame(): RoundBasedGame {
		return $this->game;
	}

	/**
	 * Gets the current state of the round
	 *
	 * @return RoundState
	 */
	public function getState(): RoundState {
		return $this->state;
	}

	/**
	 * @param RoundState $state
	 */
	public function setState(RoundState $state): void {
		$this->state = $state;
	}

	/**
	 * Gets the current round.
	 *
	 * @return Round
	 */
	public function getCurrentRound(): Round {
		return $this->current;
	}

	/**
	 * Sets the current round
	 *
	 * @param Round $round
	 * @return void
	 */
	public function setCurrentRound(Round $round): void {
		$this->current = $round;
	}

	/**
	 * Returns a list of all past rounds.
	 *
	 * @return array<Round>
	 */
	public function getPastRounds(): array {
		return $this->past;
	}

	/**
	 * Adds a round to the past rounds.
	 *
	 * @param Round $round
	 * @return void
	 */
	public function addPastRound(Round $round): void {
		$this->past[] = $round;
	}

	/**
	 * Gets the score for a team
	 *
	 * @param Team $team
	 * @return int
	 */
	public function getScore(Team $team): int {
		return $this->scores[$team->getId()] ??= 0;
	}

	/**
	 * Sets the score for a team.
	 *
	 * @param Team $team
	 * @param int $score
	 * @return void
	 */
	public function setScore(Team $team, int $score): void {
		$this->scores[$team->getId()] = $score;
	}

	public function getRoundWinner(): ?Team {
		$aliveTeams = $this->getGame()->getTeamManager()->getAliveTeams();
		return count($aliveTeams) === 1 ? $aliveTeams[array_key_first($aliveTeams)] : null;
	}

	/**
	 * Returns the number of rounds that can be played
	 *
	 * @return int
	 */
	public abstract function getRoundCount(): int;

	/**
	 * Returns the amount of time in seconds that the round should last.
	 *
	 * @return int
	 */
	public abstract function getRoundLength(): int;

	/**
	 * Returns whether the team has won the game based off of the rounds played.
	 *
	 * @param Team $team
	 * @return bool
	 */
	public abstract function hasTeamWon(Team $team): bool;

}