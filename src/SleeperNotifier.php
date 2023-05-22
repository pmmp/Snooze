<?php

/*
 * This file is part of Snooze <https://github.com/pmmp/Snooze>
 * Copyright (c) 2018-2023 PMMP Team
 *
 * Snooze is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace pocketmine\snooze;

use pmmp\thread\ThreadSafeArray;

/**
 * Used to wake up the sleeping thread from another thread.
 * Use {@link SleeperHandlerEntry::createNotifier()} inside the thread to create this.
 */
final class SleeperNotifier{

	/**
	 * @internal
	 * Do not construct this object directly. Use {@link SleeperHandlerEntry::createNotifier()} instead.
	 *
	 * @phpstan-param ThreadSafeArray<int, int> $sharedObject
	 */
	public function __construct(
		private readonly ThreadSafeArray $sharedObject,
		private readonly int $notifierId
	){}

	/**
	 * Call this method to wake up the sleeping thread.
	 */
	final public function wakeupSleeper() : void{
		$shared = $this->sharedObject;
		$sleeperId = $this->notifierId;
		$shared->synchronized(function() use ($shared, $sleeperId) : void{
			if(!isset($shared[$sleeperId])){
				$shared[$sleeperId] = $sleeperId;
				$shared->notify();
			}
		});
	}
}
