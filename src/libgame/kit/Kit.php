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

namespace libgame\kit;

use libMarshal\MarshalTrait;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\player\Player;

class Kit {
	use MarshalTrait;

	/**
	 * @param string $name
	 * @param array<Armor> $armor
	 * @param array<Item> $items
	 */
	public function __construct(
		protected string $name,
		protected array $armor,
		protected array $items
	) {
	}

	public function getName(): string {
		return $this->name;
	}

	/**
	 * @return Armor[]
	 */
	public function getArmor(): array {
		return $this->armor;
	}

	/**
	 * @return Item[]
	 */
	public function getItems(): array {
		return $this->items;
	}


	/**
	 * Gives a player the items associated with this kit.
	 *
	 * @param Player $player
	 * @return void
	 */
	public function give(Player $player): void {
		$player->getArmorInventory()->setContents($this->getArmor());
		$player->getInventory()->setContents($this->getItems());
	}

}