<?php

use WordSphere\Core\Application\ContentManagement\Commands\CreateContentCommand;
use WordSphere\Core\Application\ContentManagement\Services\CreateContentService;
use WordSphere\Core\Domain\ContentManagement\Repositories\ContentRepositoryInterface;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentContentRepository;
use WordSphere\Core\Infrastructure\Identity\Persistence\UserModel;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function (): void {
    $this->app->bind(
        abstract: ContentRepositoryInterface::class,
        concrete: EloquentContentRepository::class
    );
});

test('can create a article', function (): void {

    //Arrange
    /** @var CreateContentService $createArticleService */
    $createArticleService = $this->app->make(CreateContentService::class);

    $createdBy = Uuid::generate();

    $command = new CreateContentCommand(
        type: 'blog-posts',
        createdBy: $createdBy,
        title: 'Test EloquentContent',
        content: 'Test EloquentContent',
        excerpt: 'Test EloquentContent',
        slug: 'test-article',
        customFields: ['featured' => true]
    );

    //Act
    $articleId = $createArticleService->execute($command);

    //Assert
    expect($articleId)->toBeInstanceOf(Uuid::class);

    assertDatabaseHas('contents', [
        'id' => $articleId->toString(),
        'title' => 'Test EloquentContent',
        'content' => 'Test EloquentContent',
        'excerpt' => 'Test EloquentContent',
        'slug' => 'test-article',
    ]);

})->skip('Needs refactoring after changing the type implementation');

test('creates unique slug when not provided', function (): void {
    //Arrange
    /** @var CreateContentService $createArticleService */
    $createArticleService = $this->app->make(CreateContentService::class);
    /** @var UserModel $user */
    $user = UserModel::factory()
        ->create();
    $createdBy = Uuid::fromString($user->uuid);
    $command = new CreateContentCommand(
        type: 'blog-posts',
        createdBy: $createdBy,
        title : 'Test Content',
        content: 'Content',
        excerpt: 'Excerpt'
    );

    //Act
    $articleId = $createArticleService->execute($command);

    /** @var EloquentContentRepository $repository */
    $repository = $this->app->make(EloquentContentRepository::class);
    $article = $repository->findById($articleId);

    //Assert
    expect($article->getSlug()->toString())
        ->toBe('test-content');

    //Arrange part II
    $command2 = new CreateContentCommand(
        type: 'blog-posts',
        createdBy: $createdBy,
        title: 'Test Content',
        content: 'Different Content',
        excerpt: 'Different Excerpt',
    );

    $articleIdPartII = $createArticleService->execute($command2);
    $articleII = $repository->findById($articleIdPartII);

    expect($articleII->getSlug())
        ->not
        ->toBe('test-content')
        ->and($articleII->getSlug()->toString())
        ->toStartWith('test-content-1');

    // Create third article with same title
    $command3 = new CreateContentCommand(
        type: 'blog-posts',
        createdBy: $createdBy,
        title: 'Test Content',
        content: 'Content 3',
        excerpt: 'Excerpt 3'
    );
    $articleIdIII = $createArticleService->execute($command3);

    $articleIII = $repository->findById($articleIdIII);

    expect($articleIII->getSlug()->toString())
        ->toBe('test-content-2');

})->skip('Needs refactoring after changing the type implementation');

test('throws exception for empty title', function (): void {
    /** @var CreateContentService $createArticleService */
    $createArticleService = $this->app->make(CreateContentService::class);
    /** @var UserModel $user */
    $user = UserModel::factory()
        ->create();
    $cratedBy = Uuid::fromString($user->uuid);
    $command = new CreateContentCommand(
        type: 'blog-posts',
        createdBy: $cratedBy,
        title: '',
        content: 'Content',
        excerpt: 'Excerpt'
    );

    $this->expectException(InvalidArgumentException::class);

    $createArticleService->execute($command);

})->skip('Needs refactoring after changing the type implementation');
