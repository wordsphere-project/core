<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\ContentManagement\Adapters;

use DateTimeImmutable;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\ContentManagement\Enums\ContentStatus;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentContent as EloquentArticle;

class ContentAdapter
{
    public static function toEloquent(Content $domainArticle): EloquentArticle
    {

        $eloquentArticle = new EloquentArticle;
        $eloquentArticle->forceFill(attributes: $domainArticle->toArray());

        return $eloquentArticle;

    }

    public static function toDomain(EloquentArticle $eloquentArticle): Content
    {
        return new Content(
            id: Uuid::fromString($eloquentArticle->id),
            title: $eloquentArticle->title,
            slug: Slug::fromString($eloquentArticle->slug),
            createdBy: Uuid::fromString($eloquentArticle->created_by),
            updatedBy: Uuid::fromString($eloquentArticle->updated_by),
            content: $eloquentArticle->content,
            excerpt: $eloquentArticle->excerpt,
            customFields: $eloquentArticle->custom_fields,
            status: ContentStatus::from($eloquentArticle->status),
            publishedAt: new DateTimeImmutable($eloquentArticle->published_at->toString()),
        );
    }
}
