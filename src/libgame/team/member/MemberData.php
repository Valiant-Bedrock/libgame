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

namespace libgame\team\member;

use pocketmine\player\Player;
use pocketmine\Server;

class MemberData {

	public function __construct(
		public string $username,
		public string $uuid
	) {}

	public function getUsername(): string {
		return $this->username;
	}

	public function getUuid(): string {
		return $this->uuid;
	}

	public function getPlayer(): ?Player {
		return Server::getInstance()->getPlayerByRawUUID($this->uuid);
	}
}