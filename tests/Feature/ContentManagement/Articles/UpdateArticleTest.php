<?php

use WordSphere\Core\Application\ContentManagement\Commands\UpdateArticleCommand;
use WordSphere\Core\Application\ContentManagement\Exceptions\ArticleNotFoundException;
use WordSphere\Core\Application\ContentManagement\Services\UpdateArticleService;
use WordSphere\Core\Application\Factories\ContentManagement\ArticleFactory;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentArticleRepository;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;

beforeEach(function (): void {

    $this->app->bind(
        abstract: ArticleRepositoryInterface::class,
        concrete: EloquentArticleRepository::class
    );

    $this->articleRepository = $this->app->make(
        abstract: ArticleRepositoryInterface::class,
    );

    /** @var EloquentUser $user */
    $user = EloquentUser::factory()
        ->create();

    $this->testArticle = Content::create(
        title: 'Original Title',
        slug: Slug::fromString('original-title'),
        creator: Uuid::fromString($user->uuid),
        content: 'Original Content',
        excerpt: 'Original Excerpt',
    );

    $this->articleRepository->save($this->testArticle);

});

test('can update an article', function (): void {
    $updateArticleService = $this->app->make(
        abstract: UpdateArticleService::class
    );

    /** @var EloquentUser $user */
    $user = EloquentUser::factory()
        ->create();

    $command = new UpdateArticleCommand(
        id: Uuid::fromString($this->testArticle->getId()->toString()),
        updatedBy: Uuid::fromString($user->uuid),
        title: 'Updated Title',
        content: 'Updated Content',
        excerpt: 'Updated Excerpt',
        slug: Slug::fromString('original-title')
    );

    $updateArticleService->execute($command);

    /** @var Content $updatedArticle */
    $updatedArticle = $this->articleRepository
        ->findById($this->testArticle->getId());

    expect($updatedArticle)
        ->toBeInstanceOf(Content::class)
        ->and($updatedArticle->getTitle())->toBe('Updated Title')
        ->and($updatedArticle->getContent())->toBe('Updated Content')
        ->and($updatedArticle->getExcerpt())->toBe('Updated Excerpt')
        ->and($updatedArticle->getSlug()->toString())->toBe('original-title');

});

test('updating article with existing slug appends number to slug', function (): void {

    $this->travel(5)->days();

    ArticleFactory::new()
        ->create(
            attributes: [
                'slug' => Slug::fromString('updated-slug'),
            ]
        );

    /** @var EloquentUser $user */
    $user = EloquentUser::factory()
        ->create();

    /** @var UpdateArticleService $updateArticleService */
    $updateArticleService = $this->app->make(UpdateArticleService::class);
    $command = new UpdateArticleCommand(
        id: Uuid::fromString($this->testArticle->getId()->toString()),
        updatedBy: Uuid::fromString($user->uuid),
        title: 'Updated title',
        content: 'Updated Content',
        excerpt: 'Updated Excerpt',
        slug: Slug::fromString('updated-slug')
    );

    $updateArticleService->execute($command);

    $updatedArticle = $this->articleRepository->findById($this->testArticle->getId());

    expect($updatedArticle->getSlug()->toString())
        ->toStartWith('updated-slug-')
        ->and($updatedArticle->getSlug()->toString())->not
        ->toBe('updated-slug');

    $this->travelBack();

});

test('throws exception when updating non-existing article', function (): void {

    $updateArticleService = $this->app->make(UpdateArticleService::class);
    /** @var EloquentUser $user */
    $user = EloquentUser::factory()
        ->create();

    $command = new UpdateArticleCommand(
        id: Uuid::generate(),
        updatedBy: Uuid::fromString($user->uuid),
        title: 'Updated Title',
        content: 'Updated Content',
        excerpt: 'Updated Excerpt',
        slug: Slug::fromString('updated-slug')
    );

    $this->expectException(ArticleNotFoundException::class);

    $updateArticleService->execute($command);

});
