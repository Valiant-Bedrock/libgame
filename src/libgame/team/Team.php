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

namespace libgame\team;

use Closure;
use libgame\team\member\MemberData;
use pocketmine\player\OfflinePlayer;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use function array_combine;
use function array_filter;
use function array_keys;
use function array_map;
use function array_values;

class Team
{
	protected const PREFIX = TextFormat::YELLOW . "Team > ";

	/** @var array<string,MemberData> */
	protected array $members = [];

	/**
	 * @param Player[] $members
	 */
	public function __construct(
		protected int $id,
		protected string $color,
		array $members = []
	) {
		foreach ($members as $member)
		{
			$this->addMember($member);
		}
	}

	public static function create(int $id, string $color, Player ...$members): Team {
		return new Team(
			id: $id,
			color: $color,
			members: $members
		);
	}

	public function getId(): int {
		return $this->id;
	}

	public function getColor(): string {
		return $this->color;
	}

	public function setColor(string $color): void {
		$this->color = $color;
	}

	public function getFormattedName(): string {
		return $this->color . $this->__toString();
	}

	public function addMember(Player $member): void {
		$this->members[$member->getUniqueId()->getBytes()] = new MemberData(
			username: $member->getName(),
			uuid: $member->getUniqueId()->getBytes()
		);
	}

	public function removeMember(Player $member): void {
		unset($this->members[$member->getUniqueId()->getBytes()]);
	}

	public function removeMemberByUUID(string $uuid): void {
		unset($this->members[$uuid]);
	}

	public function isMember(Player $member): bool {
		return isset($this->members[$member->getUniqueId()->getBytes()]);
	}

	/**
	 * @return array<string, Player>
	 */
	public function getOnlineMembers(): array {
		return array_filter(
			array: $this->getMembers(),
			callback: fn(Player|OfflinePlayer $member): bool => $member instanceof Player && $member->isOnline()
		);
	}

	/**
	 *
	 * @return array<string,Player|OfflinePlayer>
	 */
	public function getMembers(): array {
		$server = Server::getInstance();
		return array_combine(
			array_keys($this->members),
			array_map(
				callback: static fn(MemberData $memberData): Player|OfflinePlayer => $server->getPlayerByRawUUID($memberData->uuid) ?? new OfflinePlayer($memberData->username, null),
				array: array_values($this->members)
			)
		);
	}

	/**
	 * @param Closure(Player): void $callback
	 */
	public function executeOnPlayers(Closure $callback): void {
		foreach($this->getOnlineMembers() as $player) {
			$callback($player);
		}
	}

	public function broadcastMessage(string $message, bool $prependPrefix = true): void {
		if($prependPrefix) {
			$message = self::PREFIX . $message;
		}

		$this->executeOnPlayers(function(Player $player) use($message): void {
			$player->sendMessage($message);
		});
	}

	public function broadcastTip(string $tip): void {
		$this->executeOnPlayers(function(Player $player) use($tip): void {
			$player->sendTip($tip);
		});
	}

	public function __toString(): string {
		return "Team #$this->id";
	}

}