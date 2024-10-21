<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Services;

use WordSphere\Core\Application\ContentManagement\Commands\ChangeContentStatusCommand;
use WordSphere\Core\Application\ContentManagement\Contracts\ContentStatusServiceInterface;
use WordSphere\Core\Application\ContentManagement\Exceptions\ArticleNotFoundException;
use WordSphere\Core\Domain\ContentManagement\Enums\ArticleStatus;
use WordSphere\Core\Domain\ContentManagement\Exceptions\InvalidArticleStatusException;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\Support\Events\LaravelEventDispatcher;

use function info;

readonly class ArticleStatusService implements ContentStatusServiceInterface
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private LaravelEventDispatcher $eventDispatcher,
    ) {}

    public function execute(ChangeContentStatusCommand $command): void
    {
        $articleId = Uuid::fromString($command->id);

        $article = $this->articleRepository->findById($articleId);

        if (! $article) {
            throw new ArticleNotFoundException($articleId);
        }

        try {
            match ($command->newStatus) {
                ArticleStatus::DRAFT => $article->unpublish($command->statusChangedBy),
                ArticleStatus::PUBLISHED => $article->publish($command->statusChangedBy),
            };
        } catch (InvalidArticleStatusException $e) {
            info($e->getMessage());
            throw $e;
        }

        $this->articleRepository->save($article);

        foreach ($article->pullDomainEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }
    }
}
