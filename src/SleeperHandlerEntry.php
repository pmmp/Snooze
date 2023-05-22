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

use pmmp\thread\ThreadSafe;
use pmmp\thread\ThreadSafeArray;

/**
 * Represents an entry in a {@link SleeperHandler}. This is used to unregister the notifier when it is no longer
 * needed. It is also used to create the {@link SleeperNotifier} for the thread that needs to do wakeups.
 *
 * Since notifiers themselves are not shared between threads, they don't need to be thread-safe. We only need to pass
 * the information needed to construct the notifier to the destination thread.
 * This approach maximizes performance by avoiding unnecessary overhead of extra ThreadSafe objects.
 *
 * Pass this object into the thread that needs to do wakeups, and then create a notifier using
 * {@link SleeperHandlerEntry::createNotifier()}.
 *
 * Obtain this by calling {@link SleeperHandler::addNotifier()}.
 */
final class SleeperHandlerEntry extends ThreadSafe{

	/**
	 * @internal
	 * Do not construct this object directly. Use {@link SleeperHandler::addNotifier()} instead.
	 *
	 * @phpstan-param ThreadSafeArray<int, int> $sharedObject
	 */
	public function __construct(
		private readonly ThreadSafeArray $sharedObject,
		private readonly int $id
	){}

	final public function getNotifierId() : int{
		return $this->id;
	}

	/**
	 * Constructs a notifier for this entry. Call this inside the thread that needs to do wakeups to get a notifier
	 * instance.
	 */
	public function createNotifier() : SleeperNotifier{
		return new SleeperNotifier($this->sharedObject, $this->id);
	}
}
