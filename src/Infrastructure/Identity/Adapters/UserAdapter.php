<?php

namespace WordSphere\Core\Infrastructure\Identity\Adapters;

use WordSphere\Core\Domain\Identity\Entities\User as DomainUser;
use WordSphere\Core\Domain\Shared\ValueObjects\Email;
use WordSphere\Core\Domain\Shared\ValueObjects\Id;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;

class UserAdapter
{
    public static function toEloquent(DomainUser $domainUser): EloquentUser
    {

        $eloquentUser = new EloquentUser;
        $eloquentUser->forceFill(attributes: $domainUser->toArray());

        return $eloquentUser;

    }

    public static function toDomain(EloquentUser $eloquentUser): DomainUser
    {
        return new DomainUser(
            id: Id::fromInt($eloquentUser->id),
            uuid: Uuid::fromString($eloquentUser->uuid),
            email: Email::fromString($eloquentUser->email),
            name: $eloquentUser->name,
        );
    }
}
