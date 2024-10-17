<?php

namespace WordSphere\Core\Infrastructure\ContentManagement\Persistence;

use DateTimeImmutable;
use WordSphere\Core\Domain\ContentManagement\Entities\Article;
use WordSphere\Core\Domain\ContentManagement\Enums\ArticleStatus;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleId;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\Article as EloquentArticle;

class EloquentArticleRepository implements ArticleRepositoryInterface
{
    public function nextIdentity(): ArticleId
    {
        return ArticleId::generate();
    }

    public function findById(ArticleId $id): ?Article
    {
        $eloquentArticle = EloquentArticle::query()
            ->find($id->toString());
        if (! $eloquentArticle) {
            return null;
        }

        return $this->toDomainEntity($eloquentArticle);
    }

    public function findBySlug(Slug $slug): ?Article
    {
        $eloquentArticle = EloquentArticle::query()
            ->where('slug', $slug->toString())
            ->first();

        return $eloquentArticle ? $this->toDomainEntity($eloquentArticle) : null;
    }

    public function save(Article $article): void
    {
        $eloquentArticle = EloquentArticle::query()->findOrNew($article->getId()->toString());
        $this->updateModelFromEntity($eloquentArticle, $article);
        $eloquentArticle->save();
    }

    public function delete(ArticleId $id): void
    {
        EloquentArticle::destroy($id->toString());
    }

    public function isSlugUnique(Slug $slug): bool
    {
        return ! EloquentArticle::query()
            ->where('slug', $slug->toString())->exists();
    }

    private function toDomainEntity(EloquentArticle $eloquentArticle): Article
    {
        $article = new Article(
            id: ArticleId::fromString($eloquentArticle->id),
            title: $eloquentArticle->title,
            slug: Slug::fromString($eloquentArticle->slug),
            content: $eloquentArticle->content,
            excerpt: $eloquentArticle->excerpt,
            data: $eloquentArticle->data,
            status: ArticleStatus::from($eloquentArticle->status),
            createdAt: DateTimeImmutable::createFromInterface($eloquentArticle->created_at),
            updatedAt: DateTimeImmutable::createFromInterface($eloquentArticle->updated_at)
        );

        if ($eloquentArticle->status === ArticleStatus::PUBLISHED->toString()) {
            $article->publish();
        }

        return $article;

    }

    private function updateModelFromEntity(EloquentArticle $eloquentArticle, Article $article): void
    {
        $eloquentArticle->id = $article->getId()->toString();
        $eloquentArticle->title = $article->getTitle();
        $eloquentArticle->slug = $article->getSlug();
        $eloquentArticle->content = $article->getContent();
        $eloquentArticle->excerpt = $article->getExcerpt();
        $eloquentArticle->status = $article->getStatus()->toString();
        $eloquentArticle->updated_at = $article->getUpdatedAt();
        $eloquentArticle->published_at = $article->getPublishedAt();

    }
}
