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

use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\player\Player;

class HotbarMenu {

	/**
	 * @param array<int, MenuEntry> $menuEntries
	 */
	public function __construct(protected array $menuEntries = []) {

	}

	/**
	 * @return array<int, Item>
	 */
	public function getItems(): array {
		return array_combine(
			keys: array_keys($this->menuEntries),
			values: array_map(
				callback: fn(MenuEntry $entry) => $entry->getItem(),
				array: $this->menuEntries
			)
		);
	}

	/**
	 * Sends the item contents to the player
	 *
	 * @param Player $player
	 * @return void
	 */
	public function send(Player $player): void {
		$player->getInventory()->setContents($this->getItems());
	}

	/**
	 * Checks if a clicked item matches a menu entry and runs it if possible
	 *
	 * @param PlayerItemUseEvent $event
	 * @return void
	 */
	public function checkAndCallItem(PlayerItemUseEvent $event): void {
		$player = $event->getPlayer();
		$item = $event->getItem();
		foreach($this->menuEntries as $entry) {
			if($entry->getItem()->equalsExact($item)) {
				$entry->run($player);
				return;
			}
		}
	}

}