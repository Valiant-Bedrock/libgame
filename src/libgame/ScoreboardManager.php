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

namespace libgame;

use libscoreboard\Scoreboard;
use LogicException;
use pocketmine\player\Player;

class ScoreboardManager implements Updatable {

	/** @var array<string, Scoreboard> */
	protected array $scoreboards = [];

	public function __construct(protected string $title) {
	}

	/**
	 * @return array<string, Scoreboard>
	 */
	public function getAll(): array {
		return $this->scoreboards;
	}

	public function getTitle(): string {
		return $this->title;
	}

	public function setTitle(string $title): void {
		$this->title = $title;
	}

	public function add(Player $player): void {
		$this->scoreboards[$player->getUniqueId()->getBytes()] = new Scoreboard(
			player: $player,
			title: $this->title,
			lines: []
		);
	}

	public function remove(Player $player): void {
		if (($scoreboard = $this->getNullable($player)) !== null) {
			$scoreboard->remove();
			unset($this->scoreboards[$player->getUniqueId()->getBytes()]);
		}
	}

	public function getNullable(Player $player): ?Scoreboard {
		return $this->scoreboards[$player->getUniqueId()->getBytes()] ?? null;
	}

	public function get(Player $player): Scoreboard {
		return $this->getNullable($player) ?? throw new LogicException("Unable to locate scoreboard for player {$player->getName()}");
	}

	public function update(): void {
		foreach ($this->getAll() as $scoreboard) {
			if (!$scoreboard->isVisible()) {
				$scoreboard->send();
			}

			$scoreboard->update();
		}
	}

	public function finish(): void {
		foreach ($this->getAll() as $key => $scoreboard) {
			$scoreboard->remove();
			unset($this->scoreboards[$key]);
		}
	}
}