<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Contracts;

interface EventDispatcherInterface
{
    public function dispatch(object $event): void;
}
