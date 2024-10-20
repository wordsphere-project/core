<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\Repositories;

use WordSphere\Core\Domain\ContentManagement\Entities\Article;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleUuid;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;

interface ArticleRepositoryInterface
{
    public function nextIdentity(): ArticleUuid;

    public function findById(ArticleUuid $id): ?Article;

    public function findByUuid(ArticleUuid $uuid): ?Article;

    public function findBySlug(Slug $slug): ?Article;

    public function save(Article $article): void;

    public function delete(ArticleUuid $id): void;

    public function isSlugUnique(Slug $slug): bool;
}
