<?php

namespace WordSphere\Core\Infrastructure\ContentManagement\Persistence;

use DateTimeImmutable;
use WordSphere\Core\Domain\ContentManagement\Entities\Article as DomainArticle;
use WordSphere\Core\Domain\ContentManagement\Enums\ArticleStatus;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleUuid;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Domain\Identity\ValueObjects\UserUuid;
use WordSphere\Core\Domain\MediaManagement\ValueObjects\Id;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\Article as EloquentArticle;

class EloquentArticleRepository implements ArticleRepositoryInterface
{
    public function nextIdentity(): ArticleUuid
    {
        return ArticleUuid::generate();
    }

    public function findById(ArticleUuid $id): ?DomainArticle
    {
        return self::findByUuid($id);
    }

    public function findByUuid(ArticleUuid $uuid): ?DomainArticle
    {
        $eloquentArticle = EloquentArticle::query()->find($uuid);
        if (! $eloquentArticle) {
            return null;
        }

        return $this->toDomainEntity($eloquentArticle);
    }

    public function findBySlug(Slug $slug): ?DomainArticle
    {
        $eloquentArticle = EloquentArticle::query()
            ->where('slug', $slug->toString())
            ->first();

        return $eloquentArticle ? $this->toDomainEntity($eloquentArticle) : null;
    }

    public function save(DomainArticle $article): void
    {
        $eloquentArticle = EloquentArticle::query()
            ->findOrNew($article->getId()->toString());
        $this->updateModelFromEntity($eloquentArticle, $article);
        $eloquentArticle->save();
    }

    public function delete(ArticleUuid $id): void
    {
        EloquentArticle::destroy($id->toString());
    }

    public function isSlugUnique(Slug $slug): bool
    {
        return ! EloquentArticle::query()
            ->where('slug', $slug->toString())->exists();
    }

    private function toDomainEntity(EloquentArticle $eloquentArticle): DomainArticle
    {

        $article = new DomainArticle(
            id: ArticleUuid::fromString($eloquentArticle->id),
            title: $eloquentArticle->title,
            slug: Slug::fromString($eloquentArticle->slug),
            createdBy: UserUuid::fromString($eloquentArticle->created_by),
            updatedBy: UserUuid::fromString($eloquentArticle->updated_by),
            content: $eloquentArticle->content,
            excerpt: $eloquentArticle->excerpt,
            customFields: $eloquentArticle->custom_fields,
            status: ArticleStatus::from($eloquentArticle->status),
            createdAt: DateTimeImmutable::createFromInterface($eloquentArticle->created_at),
            updatedAt: DateTimeImmutable::createFromInterface($eloquentArticle->updated_at)
        );

        if ($eloquentArticle->status === ArticleStatus::PUBLISHED->toString()) {
            $article->publish(UserUuid::fromString($eloquentArticle->updated_by));
        }

        if ($eloquentArticle->feature_image_id) {
            $article->updateFeaturedImage(
                featuredImageId: Id::fromInt($eloquentArticle->feature_image_id),
                updater: UserUuid::fromString($eloquentArticle->updated_by)
            );
        }

        return $article;

    }

    private function updateModelFromEntity(EloquentArticle $eloquentArticle, DomainArticle $article): void
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
        $eloquentArticle->featured_image_id = $article->getFeaturedImage();

    }
}
