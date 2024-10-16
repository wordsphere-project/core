<?php

namespace WordSphere\Core\Domain\ContentManagement\Services;

use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;

readonly class SlugGeneratorService
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
    ) {}

    public function generateUniqueSlug(string $baseSlug, ?Slug $currentSlug = null): Slug
    {

        $slug = Slug::fromString($baseSlug);

        if ($currentSlug && $slug->equals($currentSlug)) {
            return $slug;
        }

        while (! $this->articleRepository->isSlugUnique($slug)) {
            $slug = $slug->incrementSlug();
        }

        return $slug;

    }
}