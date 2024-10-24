<?php

namespace WordSphere\Core\Infrastructure\ContentManagement\Persistence;

use DateTimeImmutable;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\ContentManagement\Enums\ContentStatus;
use WordSphere\Core\Domain\ContentManagement\Repositories\ContentRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Domain\Shared\ValueObjects\Id;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentContent as EloquentArticle;

class EloquentContentRepository implements ContentRepositoryInterface
{
    public function nextIdentity(): Uuid
    {
        return Uuid::generate();
    }

    public function findById(Uuid $id): ?Content
    {
        return self::findByUuid($id);
    }

    public function findByUuid(Uuid $uuid): ?Content
    {
        $eloquentArticle = EloquentArticle::query()->find($uuid);
        if (! $eloquentArticle) {
            return null;
        }

        return $this->toDomainEntity($eloquentArticle);
    }

    public function findBySlug(Slug $slug): ?Content
    {
        $eloquentArticle = EloquentArticle::query()
            ->where('slug', $slug->toString())
            ->first();

        return $eloquentArticle ? $this->toDomainEntity($eloquentArticle) : null;
    }

    public function save(Content $article): void
    {
        $eloquentArticle = EloquentArticle::query()
            ->findOrNew($article->getId()->toString());
        $this->updateModelFromEntity($eloquentArticle, $article);
        $eloquentArticle->save();
    }

    public function delete(Uuid $id): void
    {
        EloquentArticle::destroy($id->toString());
    }

    public function isSlugUnique(Slug $slug): bool
    {
        return ! EloquentArticle::query()
            ->where('slug', $slug->toString())->exists();
    }

    private function toDomainEntity(EloquentArticle $eloquentArticle): Content
    {

        $article = new Content(
            id: Uuid::fromString($eloquentArticle->id),
            title: $eloquentArticle->title,
            slug: Slug::fromString($eloquentArticle->slug),
            createdBy: Uuid::fromString($eloquentArticle->created_by),
            updatedBy: Uuid::fromString($eloquentArticle->updated_by),
            content: $eloquentArticle->content,
            excerpt: $eloquentArticle->excerpt,
            customFields: $eloquentArticle->custom_fields,
            status: ContentStatus::from($eloquentArticle->status),
            createdAt: DateTimeImmutable::createFromInterface($eloquentArticle->created_at),
            updatedAt: DateTimeImmutable::createFromInterface($eloquentArticle->updated_at)
        );

        if ($eloquentArticle->feature_image_id) {
            $article->updateFeaturedImageId(
                featuredImageId: Id::fromInt($eloquentArticle->feature_image_id),
                updater: Uuid::fromString($eloquentArticle->updated_by)
            );
        }

        return $article;

    }

    private function updateModelFromEntity(EloquentArticle $eloquentArticle, Content $article): void
    {
        $eloquentArticle->id = $article->getId();
        $eloquentArticle->title = $article->getTitle();
        $eloquentArticle->slug = $article->getSlug();
        $eloquentArticle->content = $article->getContent();
        $eloquentArticle->excerpt = $article->getExcerpt();
        $eloquentArticle->status = $article->getStatus()->toString();
        $eloquentArticle->created_at = $article->getCreatedAt();
        $eloquentArticle->updated_at = $article->getUpdatedAt();
        $eloquentArticle->published_at = $article->getPublishedAt();
        $eloquentArticle->created_by = $article->getCreatedBy();
        $eloquentArticle->updated_by = $article->getUpdatedBy();
        $eloquentArticle->featured_image_id = $article->getFeaturedImageId();

    }
}
