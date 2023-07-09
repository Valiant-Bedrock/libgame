<?php
/**
 *  _    _ _    _  _____
 * | |  | | |  | |/ ____|
 * | |  | | |__| | |
 * | |  | |  __  | |
 * | |__| | |  | | |____
 * \____/|_|  |_|\_____|
 *
 * Copyright (C) 2020 - 2023 | Valiant Network / Matthew Jordan
 *
 * This program is private software. You may not redistribute this software, or
 * any derivative works of this software, in source or binary form, without
 * the express permission of the owner.
 *
 * @author sylvrs
 */
declare(strict_types=1);

namespace libgame\handler;

use libgame\event\GameEvent;
use libgame\game\Game;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityEvent;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

abstract class GameEventHandler extends EventHandler {

	public function __construct(protected readonly Game $game) {
	}

	public function getGame(): Game {
		return $this->game;
	}

	protected function shouldHandle(Event $event): bool {
		$game = $this->getGame();
		return match (true) {
			$event instanceof GameEvent => $event->getGame() === $game,
			$event instanceof PlayerEvent => $game->isInGame($event->getPlayer()),
			// Specific block events that need to check the players
			$event instanceof BlockBreakEvent, $event instanceof BlockPlaceEvent => $game->isInGame($event->getPlayer()) || $event->getBlock()->getPosition()->getWorld() === $game->getArena()->getWorld(),
			$event instanceof BlockEvent => $event->getBlock()->getPosition()->getWorld() === $game->getArena()->getWorld(),
			$event instanceof EntityEvent => ($entity = $event->getEntity()) instanceof Player ? $game->isInGame($entity) : $entity->getWorld() === $game->getArena()->getWorld(),
			default => true
		};
	}
}