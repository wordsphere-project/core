<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Services;

use WordSphere\Core\Application\ContentManagement\Commands\UpdateArticleCommand;
use WordSphere\Core\Application\ContentManagement\Exceptions\ArticleNotFoundException;
use WordSphere\Core\Domain\ContentManagement\Entities\Article;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\Services\SlugGeneratorService;

readonly class UpdateArticleService
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private SlugGeneratorService $slugGenerator
    ) {}

    public function execute(UpdateArticleCommand $command): void
    {
        $article = $this->articleRepository->findById($command->id);
        if (! $article) {
            throw new ArticleNotFoundException($command->id);
        }

        $this->updateArticleFields($article, $command);

        $this->articleRepository->save($article);
    }

    private function updateArticleFields(Article $article, UpdateArticleCommand $command): void
    {
        if ($command->title !== null) {
            $article->updateTitle($command->title);
        }

        if ($command->content !== null) {
            $article->updateContent($command->content);
        }

        if ($command->excerpt !== null) {
            $article->updateExcerpt($command->excerpt);
        }

        if ($command->slug !== null) {
            $newSlug = $this->slugGenerator
                ->generateUniqueSlug(
                    baseSlug: $command->slug,
                    currentSlug: $article->getSlug()
                );
            $article->updateSlug(
                newSlug: $newSlug
            );
        }

        if ($command->data !== null) {
            $article->updateData($command->data);
        }

    }
}
