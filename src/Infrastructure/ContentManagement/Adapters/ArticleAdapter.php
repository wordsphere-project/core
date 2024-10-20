<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\ContentManagement\Adapters;

use DateTimeImmutable;
use WordSphere\Core\Domain\ContentManagement\Entities\Article as DomainArticle;
use WordSphere\Core\Domain\ContentManagement\Enums\ArticleStatus;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleUuid;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Domain\Identity\ValueObjects\UserUuid;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\Article as EloquentArticle;

class ArticleAdapter
{
    public static function toEloquent(DomainArticle $domainArticle): EloquentArticle
    {

        $eloquentArticle = new EloquentArticle;
        $eloquentArticle->forceFill(attributes: $domainArticle->toArray());

        return $eloquentArticle;

    }

    public static function toDomain(EloquentArticle $eloquentArticle): DomainArticle
    {
        return new DomainArticle(
            id: ArticleUuid::fromString($eloquentArticle->id),
            title: $eloquentArticle->title,
            slug: Slug::fromString($eloquentArticle->slug),
            creator: UserUuid::fromString($eloquentArticle->created_by),
            updater: UserUuid::fromString($eloquentArticle->updated_by),
            content: $eloquentArticle->content,
            excerpt: $eloquentArticle->excerpt,
            customFields: $eloquentArticle->custom_fields,
            status: ArticleStatus::from($eloquentArticle->status),
            publishedAt: new DateTimeImmutable($eloquentArticle->published_at->toString()),
        );
    }
}
