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
        $eloquentUser = EloquentUser::query()->find($id->toInt());

        return $eloquentUser ? UserAdapter::toDomain($eloquentUser) : null;

    }

    public function findByUuid(Uuid $uuid): ?DomainUser
    {
        $eloquentUser = EloquentUser::query()
            ->where('uuid', $uuid->toString())
            ->first();

        return $eloquentUser ? UserAdapter::toDomain($eloquentUser) : null;
    }

    public function save(DomainUser $user): void
    {
        $eloquentUser = EloquentUser::query()
            ->findOrNew($user->getId()->toInt());
        $this->updateEloquentFromEntity($eloquentUser, $user);
        $eloquentUser->save();

    }

    private function updateEloquentFromEntity(EloquentUser $eloquentUser, DomainUser $user): void
    {
        $eloquentUser->id = $user->getId()->toInt();
        $eloquentUser->uuid = $user->getUuid()->toString();
        $eloquentUser->email = $user->getEmail()->toString();
        $eloquentUser->name = $user->getName();
    }
}
