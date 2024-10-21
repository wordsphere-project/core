<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Services;

use WordSphere\Core\Application\ContentManagement\Commands\CreateArticleCommand;
use WordSphere\Core\Domain\ContentManagement\Entities\Article;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\Services\SlugGeneratorService;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

readonly class CreateArticleService
{
    public function __construct(
        private ArticleRepositoryInterface $repository,
        private SlugGeneratorService $slugGenerator
    ) {}

    public function execute(CreateArticleCommand $command): Uuid
    {

        $slug = $this->slugGenerator->generateUniqueSlug(
            baseSlug: $command->slug ?? $command->title,
        );

        $article = Article::create(
            title: $command->title,
            slug: $slug,
            creator: $command->createdBy,
            content: $command->content,
            excerpt: $command->excerpt,
            customFields: $command->customFields,
            featuredImage: $command->featuredImage
        );

        $this->repository->save($article);

        return $article->getId();

    }
}
