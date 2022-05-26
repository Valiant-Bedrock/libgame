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

namespace libgame\menu;

use Closure;
use pocketmine\item\Item;
use pocketmine\player\Player;

class MenuEntry {

	public function __construct(protected Item $item, protected Closure $closure) {
	}

	/**
	 * @return Item
	 */
	public function getItem(): Item {
		return $this->item;
	}

	/**
	 * @param Player $player
	 * @return void
	 */
	public function run(Player $player): void {
		($this->closure)($player);
	}

}