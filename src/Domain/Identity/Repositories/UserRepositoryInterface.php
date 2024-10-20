<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Identity\Repositories;

use WordSphere\Core\Domain\Identity\Entities\User;
use WordSphere\Core\Domain\Shared\ValueObjects\Id;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

interface UserRepositoryInterface
{
    public function findById(Id $id): ?User;

    public function findByUuid(Uuid $uuid): ?User;

    public function save(User $user): void;
}
