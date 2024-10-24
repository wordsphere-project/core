<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Services;

use Illuminate\Contracts\Events\Dispatcher;
use InvalidArgumentException;
use WordSphere\Core\Application\ContentManagement\Commands\UpdateContentCommand;
use WordSphere\Core\Application\ContentManagement\Exceptions\ContentNotFoundException;
use WordSphere\Core\Domain\ContentManagement\Repositories\ContentRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\Services\SlugGeneratorService;

readonly class UpdateContentService
{
    public function __construct(
        private ContentRepositoryInterface $articleRepository,
        private SlugGeneratorService $slugGenerator,
        private Dispatcher $dispatcher
    ) {}

    public function execute(UpdateContentCommand $command): void
    {
        $article = $this->articleRepository->findById($command->id);
        if (! $article) {
            throw new ContentNotFoundException($command->id);
        }

        foreach ($command->getUpdatedFields() as $field) {
            match ($field) {
                'title' => $article->updateTitle($command->title, $command->updatedBy),
                'content' => $article->updateContent($command->content, $command->updatedBy),
                'excerpt' => $article->updateExcerpt($command->excerpt, $command->updatedBy),
                'slug' => $article->updateSlug(
                    $this->slugGenerator
                        ->generateUniqueSlug(
                            baseSlug: $command->slug,
                            currentSlug: $article->getSlug()
                        ),
                    $command->updatedBy
                ),
                'customFields' => $article->updateCustomFields($command->customFields, $command->updatedBy),
                'featuredImage' => $article->updateFeaturedImageId($command->featuredImage, $command->updatedBy),
                default => throw new InvalidArgumentException("Unexpected field: $field")
            };
        }

        $this->articleRepository->save($article);

        foreach ($article->pullDomainEvents() as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}
