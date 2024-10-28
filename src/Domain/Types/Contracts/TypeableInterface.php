<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Types\Contracts;

use WordSphere\Core\Domain\Shared\Contracts\ProjectAware;
use WordSphere\Core\Domain\Shared\Contracts\TenantAware;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

interface TypeableInterface extends ProjectAware, TenantAware
{
    public function getTypeId(): Uuid;

    public function getTypeKey(): string;

    public function getTypeEntityClass(): string;
}
