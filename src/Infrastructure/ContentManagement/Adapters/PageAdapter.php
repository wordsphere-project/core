<?php

namespace WordSphere\Core\Infrastructure\ContentManagement\Adapters;

use DateTimeImmutable;
use WordSphere\Core\Domain\ContentManagement\Entities\Page as DomainPage;
use WordSphere\Core\Domain\ContentManagement\Repositories\MediaRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Domain\Shared\ValueObjects\Id;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentPage;

class PageAdapter
{
    public static function toEloquent(DomainPage $domainPage): EloquentPage
    {

        $eloquentPage = new EloquentPage;
        $eloquentPage->forceFill(attributes: $domainPage->toArray());

        return $eloquentPage;

    }

    public static function toDomain(EloquentPage $eloquentPage): DomainPage
    {

        $mediaRepository = app(MediaRepositoryInterface::class);

        return new DomainPage(
            id: Uuid::fromString($eloquentPage->id),
            title: $eloquentPage->title,
            slug: Slug::fromString($eloquentPage->slug),
            path: $eloquentPage->path,
            createdBy: Uuid::fromString($eloquentPage->created_by),
            updatedBy: Uuid::fromString($eloquentPage->updated_by),
            content: $eloquentPage->content,
            excerpt: $eloquentPage->excerpt,
            customFields: $eloquentPage->custom_fields,
            template: $eloquentPage->template,
            sortOrder: $eloquentPage->sort_order,
            redirectUrl: $eloquentPage->redirect_url,
            featuredImage: $eloquentPage->featuredImage !== null ? $mediaRepository->findById(Id::fromInt($eloquentPage->featuredImage->id)) : null,
            status: $eloquentPage->status,
            visibility: $eloquentPage->visibility,
            publishedAt: $eloquentPage->published_at !== null ? new DateTimeImmutable($eloquentPage->published_at->toString()) : null,
            createdAt: new DateTimeImmutable($eloquentPage->created_at->toString()),
            updatedAt: new DateTimeImmutable($eloquentPage->updated_at->toString())
        );
    }
}
