<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Services;

use Illuminate\Contracts\Events\Dispatcher;
use InvalidArgumentException;
use WordSphere\Core\Application\ContentManagement\Commands\UpdateArticleCommand;
use WordSphere\Core\Application\ContentManagement\Exceptions\ArticleNotFoundException;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\Services\SlugGeneratorService;

readonly class UpdateArticleService
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private SlugGeneratorService $slugGenerator,
        private Dispatcher $dispatcher
    ) {}

    public function execute(UpdateArticleCommand $command): void
    {
        $article = $this->articleRepository->findById($command->id);
        if (! $article) {
            throw new ArticleNotFoundException($command->id);
        }

        foreach ($command->getUpdatedFields() as $field) {
            match ($field) {
                'title' => $article->updateTitle($command->title, $command->updater),
                'content' => $article->updateContent($command->content, $command->updater),
                'excerpt' => $article->updateExcerpt($command->excerpt, $command->updater),
                'slug' => $article->updateSlug(
                    $this->slugGenerator
                        ->generateUniqueSlug(
                            baseSlug: $command->slug,
                            currentSlug: $article->getSlug()
                        ),
                    $command->updater
                ),
                'customFields' => $article->updateCustomFields($command->customFields, $command->updater),
                default => throw new InvalidArgumentException("Unexpected field: $field")
            };
        }

        $this->articleRepository->save($article);

        foreach ($article->pullDomainEvents() as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}
