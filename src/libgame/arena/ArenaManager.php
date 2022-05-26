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

namespace libgame\arena;

abstract class ArenaManager {

	/** @var array<int, Arena> */
	protected array $arenas = [];
	/**
	 * A map of array IDs to booleans to determine if the arena is occupied.
	 * @var array<int, bool>
	 */
	protected array $occupied = [];

	/**
	 * Used to load the arenas from somewhere (database, file, etc)
	 *
	 * @return void
	 */
	public abstract function load(): void;

	/**
	 * Used to save the arenas to somewhere (database, file, etc)
	 *
	 * @return void
	 */
	public abstract function save(): void;

	/**
	 * Adds an arena to the manager.
	 *
	 * @param Arena $arena
	 * @return void
	 */
	public function add(Arena $arena): void {
		$this->arenas[spl_object_id($arena)] = $arena;
	}

	/**
	 * Removes an arena from the manager.
	 *
	 * @param Arena $arena
	 * @return void
	 */
	public function remove(Arena $arena): void {
		unset($this->arenas[spl_object_id($arena)]);
	}

	/**
	 * Returns true if the arena is occupied.
	 *
	 * @param Arena $arena
	 * @return bool
	 */
	public function isOccupied(Arena $arena): bool {
		return isset($this->occupied[spl_object_id($arena)]);
	}

	/**
	 * Sets an arena's occupied status.
	 *
	 * @param Arena $arena
	 * @param bool $occupied
	 * @return void
	 */
	public function setOccupied(Arena $arena, bool $occupied): void {
		if($occupied) {
			$this->occupied[spl_object_id($arena)] = true;
		} else {
			unset($this->occupied[spl_object_id($arena)]);
		}
	}

	/**
	 * Attempts to find an open arena.
	 *
	 * @return Arena|null
	 */
	public function findOpenArena(): ?Arena {
		foreach($this->arenas as $arena) {
			if(!$this->isOccupied($arena)) {
				return $arena;
			}
		}
		return null;
	}
}