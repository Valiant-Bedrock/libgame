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

namespace libgame\handler;

use libgame\GameBase;
use pocketmine\event\Event;
use pocketmine\event\EventPriority;
use pocketmine\event\HandlerListManager;
use pocketmine\event\Listener;
use pocketmine\event\ListenerMethodTags;
use pocketmine\utils\Utils;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use RuntimeException;

abstract class EventHandler implements Listener {

	/**
	 * This flag is used to determine if the event handler is registered
	 * @var bool
	 */
	private bool $registered = false;

	/**
	 * This method gets a list of all compatible events and registers the corresponding event handlers.
	 *
	 * @param GameBase $plugin
	 * @return void
	 */
	public function register(GameBase $plugin): void {
		if($this->registered) {
			throw new RuntimeException("Event handler is already registered");
		}

		$pluginManager = $plugin->getServer()->getPluginManager();
		foreach($this->getHandledEvents() as $handled) {
			$parsed = ($comment = $handled->method->getDocComment()) !== false ? Utils::parseDocComment($comment) : [];
			$pluginManager->registerEvent(
				event: $handled->eventClass,
				handler: function(Event $event) use($handled): void {
					if($this->shouldHandle($event)) {
						$handled->method->invoke($this, $event);
					}
				},
				priority: intval($parsed[ListenerMethodTags::PRIORITY] ?? EventPriority::NORMAL),
				plugin: $plugin,
				handleCancelled: boolval($parsed[ListenerMethodTags::HANDLE_CANCELLED] ?? false)
			);
		}
	}

	/**
	 * Unregisters the event handler from the global manager.
	 *
	 * @return void
	 */
	public function unregister(): void {
		if(!$this->registered) {
			throw new RuntimeException("Event handler is not registered");
		}
		HandlerListManager::global()->unregisterAll($this);
	}

	/**
	 * @return array<HandledEvent>
	 */
	private function getHandledEvents(): array {
		return array_filter(
			array: array_map(
				callback: function(ReflectionMethod $method): ?HandledEvent {
					$parameter = $method->getParameters()[array_key_first($method->getParameters())] ?? null;
					if($parameter instanceof ReflectionParameter && ($type = $parameter->getType()) instanceof ReflectionNamedType && is_a(object_or_class: $type->getName(), class: Event::class, allow_string: true)) {
						return new HandledEvent(eventClass: $type->getName(), method: $method);
					}
					return null;
				},
				array: (new ReflectionClass($this))->getMethods(ReflectionMethod::IS_PUBLIC)
			)
		);
	}

	/**
	 * This method determines if the event should be handled by this handler.
	 *
	 * @param Event $event
	 * @return bool
	 */
	protected abstract function shouldHandle(Event $event): bool;
}