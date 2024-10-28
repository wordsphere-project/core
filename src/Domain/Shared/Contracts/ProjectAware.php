<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Shared\Contracts;

use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

interface ProjectAware
{
    public function getProjectId(): Uuid;
}
