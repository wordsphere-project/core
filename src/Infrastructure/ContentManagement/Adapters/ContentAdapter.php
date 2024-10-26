<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\ContentManagement\Adapters;

use DateTimeImmutable;
use WordSphere\Core\Domain\ContentManagement\ContentTypeRegistry;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\ContentManagement\Enums\ContentStatus;
use WordSphere\Core\Domain\ContentManagement\Repositories\MediaRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Domain\Shared\ValueObjects\Id;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentContent;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentMedia;

use function app;
use function property_exists;

class ContentAdapter
{
    public static function toEloquent(Content $domainContent): EloquentContent
    {

        $eloquentContent = EloquentContent::query()
            ->with(['media' => function ($query): void {
                $query->orderBy('order');
            }])
            ->findOrNew($domainContent->getId()->toString());

        $eloquentContent->forceFill([
            'id' => $domainContent->getId()->toString(),
            'type' => $domainContent->getType()->key,
            'title' => $domainContent->getTitle(),
            'slug' => $domainContent->getSlug(),
            'content' => $domainContent->getContent(),
            'excerpt' => $domainContent->getExcerpt(),
            'status' => $domainContent->getStatus()->value,
            'custom_fields' => $domainContent->getCustomFields(),
            'featured_image_id' => $domainContent->getFeaturedImage()?->getId(),
            'created_by' => $domainContent->getCreatedBy()->toString(),
            'updated_by' => $domainContent->getUpdatedBy()->toString(),
            'created_at' => $domainContent->getCreatedAt(),
            'updated_at' => $domainContent->getUpdatedAt(),
        ]);

        return $eloquentContent;

    }

    public static function toDomain(EloquentContent $eloquentArticle): Content
    {

        $content = new Content(
            id: Uuid::fromString($eloquentArticle->id),
            type: app(ContentTypeRegistry::class)->get($eloquentArticle->type),
            title: $eloquentArticle->title,
            slug: Slug::fromString($eloquentArticle->slug),
            createdBy: Uuid::fromString($eloquentArticle->created_by),
            updatedBy: Uuid::fromString($eloquentArticle->updated_by),
            content: $eloquentArticle->content,
            excerpt: $eloquentArticle->excerpt,
            customFields: $eloquentArticle->custom_fields,
            status: ContentStatus::from($eloquentArticle->status),
            publishedAt: $eloquentArticle->published_at !== null ? new DateTimeImmutable($eloquentArticle->published_at->toString()) : null,
            createdAt: DateTimeImmutable::createFromInterface($eloquentArticle->created_at),
            updatedAt: DateTimeImmutable::createFromInterface($eloquentArticle->updated_at),
        );

        if ($eloquentArticle->feature_image_id) {

            $content->updateFeaturedImage(
                featuredImage: app(MediaRepositoryInterface::class)
                    ->findById(Id::fromInt($eloquentArticle->feature_image_id))
            );
        }

        foreach ($eloquentArticle->media as $media) {
            if (property_exists($media, 'pivot') && $media instanceof EloquentMedia) {
                $content->addMedia(MediaAdapter::fromCurator($media, $media->pivot->attributes));
            }
        }

        return $content;
    }
}
