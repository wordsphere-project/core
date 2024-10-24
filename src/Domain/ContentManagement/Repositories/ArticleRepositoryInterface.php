<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\Repositories;

use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleUuid;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

interface ArticleRepositoryInterface
{
    public function nextIdentity(): ArticleUuid;

    public function findById(Uuid $id): ?Content;

    public function findByUuid(Uuid $uuid): ?Content;

    public function findBySlug(Slug $slug): ?Content;

    public function save(Content $article): void;

    public function delete(ArticleUuid $id): void;

    public function isSlugUnique(Slug $slug): bool;
}
