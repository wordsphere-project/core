<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Identity\Repositories;

use WordSphere\Core\Domain\Identity\Entities\User;
use WordSphere\Core\Domain\Identity\ValueObjects\UserId;
use WordSphere\Core\Domain\Identity\ValueObjects\UserUuid;

interface UserRepositoryInterface
{
    public function findById(UserId $id): ?User;

    public function findByUuid(UserUuid $uuid): ?User;

    public function save(User $user): void;
}
