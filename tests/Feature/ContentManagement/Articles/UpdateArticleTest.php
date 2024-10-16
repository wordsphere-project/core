<?php

use WordSphere\Core\Application\ContentManagement\Commands\UpdateArticleCommand;
use WordSphere\Core\Application\ContentManagement\Services\UpdateArticleService;
use WordSphere\Core\Application\Factories\ContentManagement\ArticleEntityFactory;
use WordSphere\Core\Domain\ContentManagement\Entities\Article;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleId;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentArticleRepository;

beforeEach(function (): void {

    $this->app->bind(
        abstract: ArticleRepositoryInterface::class,
        concrete: EloquentArticleRepository::class
    );

    $this->articleRepository = $this->app->make(
        abstract: ArticleRepositoryInterface::class,
    );

    $this->testArticle = Article::create(
        title: 'Original Title',
        slug: Slug::fromString('original-title'),
        content: 'Original Content',
        excerpt: 'Original Excerpt',
    );

    $this->articleRepository->save($this->testArticle);

});

test('can update an article', function (): void {
    $updateArticleService = $this->app->make(
        abstract: UpdateArticleService::class
    );

    $command = new UpdateArticleCommand(
        id: ArticleId::fromString($this->testArticle->getId()->toString()),
        title: 'Updated Title',
        content: 'Updated Content',
        excerpt: 'Updated Excerpt',
        slug: Slug::fromString('original-title')
    );

    $updateArticleService->execute($command);

    /** @var Article $updatedArticle */
    $updatedArticle = $this->articleRepository
        ->findById($this->testArticle->getId());

    expect($updatedArticle)
        ->toBeInstanceOf(Article::class)
        ->and($updatedArticle->getTitle())->toBe('Updated Title')
        ->and($updatedArticle->getContent())->toBe('Updated Content')
        ->and($updatedArticle->getExcerpt())->toBe('Updated Excerpt')
        ->and($updatedArticle->getSlug()->toString())->toBe('original-title');

});

test('updating article with existing slug appends number to slug', function(): void {

    ArticleEntityFactory::new()
        ->create(
            attributes: [
                'slug' => Slug::fromString('updated-slug'),
            ]
        );

    /** @var UpdateArticleService $updateArticleService */
    $updateArticleService = $this->app->make(UpdateArticleService::class);
    $command = new UpdateArticleCommand(
        id: ArticleId::fromString($this->testArticle->getId()->toString()),
        title: 'Updated title',
        content: 'Updated Content',
        excerpt: 'Updated Excerpt',
        slug: Slug::fromString('updated-slug')
    );

    $updateArticleService->execute($command);

    $updatedArticle = $this->articleRepository->findById($this->testArticle->getId());

    expect($updatedArticle->getSlug()->toString())
        ->toStartWith('updated-slug-')
        ->and($updatedArticle->getSlug()->toString())
        ->not
        ->toBe('updated-slug');

});
