<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\Repositories;

use WordSphere\Core\Domain\ContentManagement\Entities\Article;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleId;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;

interface ArticleRepositoryInterface
{
    public function nextIdentity(): ArticleId;

    public function findById(ArticleId $id): ?Article;

    public function findBySlug(Slug $slug): ?Article;

    public function save(Article $article): void;

    public function delete(ArticleId $id): void;

    public function isSlugUnique(Slug $slug): bool;
}
