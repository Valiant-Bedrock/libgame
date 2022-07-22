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

namespace libgame\utilities;

use Closure;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use pocketmine\scheduler\TaskScheduler;
use RuntimeException;

class DeployableClosure {

	protected ?TaskHandler $currentHandler = null;

	/**
	 * @param Closure(): void $closure
	 */
	public function __construct(protected Closure $closure, protected TaskScheduler $scheduler)
	{
	}

	/**
	 * This method will schedule the closure with the provided arguments
	 */
	public function deploy(int $period, int $delay = -1): void {
		if ($this->currentHandler !== null) {
			throw new RuntimeException("Closure has already been deployed");
		}
		$task = new ClosureTask($this->closure);
		$handler = $this->scheduler->scheduleDelayedRepeatingTask(
			task: $task,
			delay: $delay,
			period: $period
		);
		$this->currentHandler = $handler;
	}

	/**
	 * This method will cancel the closure
	 */
	public function cancel(): void {
		if ($this->currentHandler === null) {
			throw new RuntimeException("Cannot cancel a task that has not been deployed");
		}
		$this->currentHandler->cancel();
		$this->currentHandler = null;
	}
}