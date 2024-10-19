<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\ContentManagement\Adapters;

use DateTimeImmutable;
use WordSphere\Core\Domain\ContentManagement\Entities\Article as DomainArticle;
use WordSphere\Core\Domain\ContentManagement\Enums\ArticleStatus;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleId;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
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
            id: ArticleId::fromString($eloquentArticle->id),
            title: $eloquentArticle->title,
            slug: Slug::fromString($eloquentArticle->slug),
            content: $eloquentArticle->content,
            excerpt: $eloquentArticle->excerpt,
            data: $eloquentArticle->data,
            status: ArticleStatus::from($eloquentArticle->status),
            createdAt: new DateTimeImmutable($eloquentArticle->created_at->toString()),
            updatedAt: new DateTimeImmutable($eloquentArticle->updated_at->toString()),
            publishedAt: new DateTimeImmutable($eloquentArticle->published_at->toString()),
        );
    }
}
