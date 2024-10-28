<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Services;

use WordSphere\Core\Application\ContentManagement\Commands\ChangeContentStatusCommand;
use WordSphere\Core\Application\ContentManagement\Contracts\ContentStatusServiceInterface;
use WordSphere\Core\Application\ContentManagement\Exceptions\ContentNotFoundException;
use WordSphere\Core\Domain\ContentManagement\Enums\ContentStatus;
use WordSphere\Core\Domain\ContentManagement\Exceptions\InvalidContentStatusException;
use WordSphere\Core\Domain\ContentManagement\Repositories\ContentRepositoryInterface;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\Shared\Events\LaravelEventDispatcher;

use function info;

readonly class ContentStatusService implements ContentStatusServiceInterface
{
    public function __construct(
        private ContentRepositoryInterface $articleRepository,
        private LaravelEventDispatcher $eventDispatcher,
    ) {}

    public function execute(ChangeContentStatusCommand $command): void
    {
        $articleId = Uuid::fromString($command->id);

        $article = $this->articleRepository->findById($articleId);

        if (! $article) {
            throw new ContentNotFoundException($articleId);
        }

        try {
            match ($command->newStatus) {
                ContentStatus::DRAFT => $article->unpublish($command->statusChangedBy),
                ContentStatus::PUBLISHED => $article->publish($command->statusChangedBy),
            };
        } catch (InvalidContentStatusException $e) {
            info($e->getMessage());
            throw $e;
        }

        $this->articleRepository->save($article);

        foreach ($article->pullDomainEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }
    }
}
