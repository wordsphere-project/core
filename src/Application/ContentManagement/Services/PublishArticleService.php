<?php

namespace WordSphere\Core\Application\ContentManagement\Services;

use Illuminate\Events\Dispatcher;
use WordSphere\Core\Application\ContentManagement\Commands\PublishArticleCommand;
use WordSphere\Core\Application\ContentManagement\Exceptions\ArticleNotFoundException;
use WordSphere\Core\Domain\ContentManagement\Exceptions\InvalidArticleStatusException;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleId;

readonly class PublishArticleService
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private Dispatcher $dispatcher,
    ) {}

    public function execute(PublishArticleCommand $command): void
    {

        $articleId = ArticleId::fromString($command->id);

        $article = $this->articleRepository->findById($articleId);

        if (! $article) {
            throw new ArticleNotFoundException($articleId);
        }

        try {
            $article->publish();
        } catch (InvalidArticleStatusException $e) {
            info($e->getMessage());
            throw $e;
        }

        $this->articleRepository->save($article);

        foreach ($article->pullDomainEvents() as $event) {
            $this->dispatcher->dispatch($event);
        }

    }
}
