<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Services;

use Illuminate\Contracts\Events\Dispatcher;
use InvalidArgumentException;
use WordSphere\Core\Application\ContentManagement\Commands\UpdateContentCommand;
use WordSphere\Core\Application\ContentManagement\Exceptions\ContentNotFoundException;
use WordSphere\Core\Domain\ContentManagement\Repositories\ContentRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\Repositories\MediaRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\Services\SlugGeneratorService;

readonly class UpdateContentService
{
    public function __construct(
        private ContentRepositoryInterface $contentRepository,
        private MediaRepositoryInterface $mediaRepository,
        private SlugGeneratorService $slugGenerator,
        private Dispatcher $dispatcher
    ) {}

    public function execute(UpdateContentCommand $command): void
    {
        $content = $this->contentRepository->findById($command->id);

        if (! $content) {
            throw new ContentNotFoundException($command->id);
        }

        foreach ($command->getUpdatedFields() as $field) {
            match ($field) {
                'title' => $content->updateTitle($command->title, $command->updatedBy),
                'content' => $content->updateContent($command->content, $command->updatedBy),
                'excerpt' => $content->updateExcerpt($command->excerpt, $command->updatedBy),
                'slug' => $content->updateSlug(
                    $this->slugGenerator
                        ->generateUniqueSlug(
                            baseSlug: $command->slug,
                            currentSlug: $content->getSlug()
                        ),
                    $command->updatedBy
                ),
                'customFields' => $content->updateCustomFields($command->customFields, $command->updatedBy),
                'featuredImage' => $content->updateFeaturedImage($this->mediaRepository->findById($command->featuredImage)),
                'media' => $content->updateMedia($command->media, $command->updatedBy),
                default => throw new InvalidArgumentException("Unexpected field: $field")
            };
        }

        $this->contentRepository->save($content);

        foreach ($content->pullDomainEvents() as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}
