<?php

namespace WordSphere\Core\Infrastructure\Shared\Events;

use Illuminate\Events\Dispatcher as EventDispatcher;
use WordSphere\Core\Domain\Shared\Contracts\EventDispatcherInterface;

readonly class LaravelEventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private EventDispatcher $eventDispatcher,
    ) {}

    public function dispatch(object $event): void
    {
        $this->eventDispatcher->dispatch($event);
    }
}
