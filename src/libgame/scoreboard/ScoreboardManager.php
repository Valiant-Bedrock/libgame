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

namespace libgame\scoreboard;

use libgame\game\Game;
use libgame\game\GameTrait;
use libgame\interfaces\Updatable;
use libscoreboard\Scoreboard;
use pocketmine\player\Player;

class ScoreboardManager implements Updatable {
	use GameTrait;

	/** @var array<string, Scoreboard> */
	protected array $scoreboards = [];

	public function __construct(Game $game, protected string $title) {
		$this->setGame($game);
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
		if(($scoreboard = $this->get($player)) !== null) {
			$scoreboard->remove();
			unset($this->scoreboards[$player->getUniqueId()->getBytes()]);
		}
	}

	public function get(Player $player): ?Scoreboard {
		return $this->scoreboards[$player->getUniqueId()->getBytes()];
	}

	public function update(): void {
		foreach ($this->getAll() as $scoreboard) {
			if (!$scoreboard->isVisible()) {
				$scoreboard->send();
			}

			$scoreboard->update();
		}
	}
}