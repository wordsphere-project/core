<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\Repositories;

use WordSphere\Core\Domain\ContentManagement\ValueObjects\Media;
use WordSphere\Core\Domain\Shared\ValueObjects\Id;

interface MediaRepositoryInterface
{
    public function findById(Id $id): ?Media;
}
