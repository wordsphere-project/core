<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Identity\Persistence;

use WordSphere\Core\Domain\Identity\Entities\User as DomainUser;
use WordSphere\Core\Domain\Identity\Repositories\UserRepositoryInterface;
use WordSphere\Core\Domain\Shared\ValueObjects\Id;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\Identity\Adapters\UserAdapter;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(Id $id): ?DomainUser
    {
        $eloquentUser = UserModel::query()->find($id->toInt());

        return $eloquentUser ? UserAdapter::toDomain($eloquentUser) : null;

    }

    public function findByUuid(Uuid $uuid): ?DomainUser
    {
        $eloquentUser = UserModel::query()
            ->where('uuid', $uuid->toString())
            ->first();

        return $eloquentUser ? UserAdapter::toDomain($eloquentUser) : null;
    }

    public function save(DomainUser $user): void
    {
        $eloquentUser = UserModel::query()
            ->findOrNew($user->getId()->toInt());
        $this->updateEloquentFromEntity($eloquentUser, $user);
        $eloquentUser->save();

    }

    private function updateEloquentFromEntity(UserModel $eloquentUser, DomainUser $user): void
    {
        $eloquentUser->id = $user->getId()->toInt();
        $eloquentUser->uuid = $user->getUuid()->toString();
        $eloquentUser->email = $user->getEmail()->toString();
        $eloquentUser->name = $user->getName();
    }
}
