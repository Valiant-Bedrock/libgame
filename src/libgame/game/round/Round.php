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

class Round {

	public function __construct(protected int $number, protected int $time = 0) {

	}

	public function getNumber(): int {
		return $this->number;
	}

	/**
	 * Increments the current time by 1.
	 *
	 * @return void
	 */
	public function incrementTime(): void {
		$this->time++;
	}

	/**
	 * Formats the round time into a minutes:seconds format.
	 *
	 * @return string
	 */
	public function formatTime(): string {
		return gmdate("i:s", $this->time);
	}

}