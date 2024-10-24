<?php

use WordSphere\Core\Application\ContentManagement\Commands\UpdateContentCommand;
use WordSphere\Core\Application\ContentManagement\Exceptions\ContentNotFoundException;
use WordSphere\Core\Application\ContentManagement\Services\UpdateContentService;
use WordSphere\Core\Application\Factories\ContentManagement\ContentFactory;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\ContentManagement\Repositories\ContentRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\Slug;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentContentRepository;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;

beforeEach(function (): void {

    $this->app->bind(
        abstract: ContentRepositoryInterface::class,
        concrete: EloquentContentRepository::class
    );

    $this->articleRepository = $this->app->make(
        abstract: ContentRepositoryInterface::class,
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
        abstract: UpdateContentService::class
    );

    /** @var EloquentUser $user */
    $user = EloquentUser::factory()
        ->create();

    $command = new UpdateContentCommand(
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

    ContentFactory::new()
        ->create(
            attributes: [
                'slug' => Slug::fromString('updated-slug'),
            ]
        );

    /** @var EloquentUser $user */
    $user = EloquentUser::factory()
        ->create();

    /** @var UpdateContentService $updateArticleService */
    $updateArticleService = $this->app->make(UpdateContentService::class);
    $command = new UpdateContentCommand(
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

    $updateArticleService = $this->app->make(UpdateContentService::class);
    /** @var EloquentUser $user */
    $user = EloquentUser::factory()
        ->create();

    $command = new UpdateContentCommand(
        id: Uuid::generate(),
        updatedBy: Uuid::fromString($user->uuid),
        title: 'Updated Title',
        content: 'Updated Content',
        excerpt: 'Updated Excerpt',
        slug: Slug::fromString('updated-slug')
    );

    $this->expectException(ContentNotFoundException::class);

    $updateArticleService->execute($command);

});
