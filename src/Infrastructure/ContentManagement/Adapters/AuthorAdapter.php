<?php

namespace WordSphere\Core\Infrastructure\ContentManagement\Adapters;

use WordSphere\Core\Domain\ContentManagement\Entities\Author as DomainAuthor;
use WordSphere\Core\Domain\Shared\ValueObjects\Id;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentAuthor;

class AuthorAdapter
{
    public static function toEloquent(DomainAuthor $domainAuthor): EloquentAuthor
    {

        $eloquentAuthor = new EloquentAuthor;
        $eloquentAuthor->forceFill(attributes: $domainAuthor->toArray());

        return $eloquentAuthor;

    }

    public static function toDomain(EloquentAuthor $eloquentAuthor): DomainAuthor
    {
        return new DomainAuthor(
            id: Uuid::fromString($eloquentAuthor->id),
            name: $eloquentAuthor->name,
            createdBy: Uuid::fromString($eloquentAuthor->created_by),
            updatedBy: Uuid::fromString($eloquentAuthor->updated_by),
            email: $eloquentAuthor->email,
            bio: $eloquentAuthor->bio,
            website: $eloquentAuthor->website,
            featuredImage: $eloquentAuthor->featured_image_id ? Id::fromInt($eloquentAuthor->featured_image_id): null,
            socialLinks: $eloquentAuthor->social_links
        );
    }
}
