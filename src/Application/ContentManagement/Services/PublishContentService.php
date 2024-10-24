<?php

namespace WordSphere\Core\Application\ContentManagement\Services;

use Illuminate\Events\Dispatcher;
use WordSphere\Core\Application\ContentManagement\Commands\PublishContentCommand;
use WordSphere\Core\Application\ContentManagement\Exceptions\ContentNotFoundException;
use WordSphere\Core\Domain\ContentManagement\Exceptions\InvalidContentStatusException;
use WordSphere\Core\Domain\ContentManagement\Repositories\ContentRepositoryInterface;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

readonly class PublishContentService
{
    public function __construct(
        private ContentRepositoryInterface $articleRepository,
        private Dispatcher $dispatcher,
    ) {}

    public function execute(PublishContentCommand $command): void
    {

        $articleId = Uuid::fromString($command->id);

        $article = $this->articleRepository->findById($articleId);

        if (! $article) {
            throw new ContentNotFoundException($articleId);
        }

        try {
            $article->publish($command->publishedBy);
        } catch (InvalidContentStatusException $e) {
            info($e->getMessage());
            throw $e;
        }

        $this->articleRepository->save($article);

        foreach ($article->pullDomainEvents() as $event) {
            $this->dispatcher->dispatch($event);
        }

    }
}
