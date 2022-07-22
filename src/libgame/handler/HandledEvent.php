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

namespace libgame\handler;

use pocketmine\event\Event;
use ReflectionMethod;

/**
 * This class is used as a way to register context-sensitive event handlers.
 */
class HandledEvent {

	/**
	 * @param class-string<Event> $eventClass
	 */
	public function __construct(public string $eventClass, public ReflectionMethod $method) {
	}

}