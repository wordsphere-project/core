<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Services;

use WordSphere\Core\Application\ContentManagement\Commands\CreateContentCommand;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\ContentManagement\Repositories\ContentRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\Services\SlugGeneratorService;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

readonly class CreateContentService
{
    public function __construct(
        private ContentRepositoryInterface $repository,
        private SlugGeneratorService $slugGenerator
    ) {}

    public function execute(CreateContentCommand $command): Uuid
    {

        $slug = $this->slugGenerator->generateUniqueSlug(
            baseSlug: $command->slug ?? $command->title,
        );

        $article = Content::create(
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
