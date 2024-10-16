<?php

use WordSphere\Core\Application\ContentManagement\Commands\CreateArticleCommand;
use WordSphere\Core\Application\ContentManagement\Services\CreateArticleService;
use WordSphere\Core\Domain\ContentManagement\Repositories\ArticleRepositoryInterface;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleId;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentArticleRepository;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->app->bind(
        abstract: ArticleRepositoryInterface::class,
        concrete: EloquentArticleRepository::class
    );
});

test('can create a article', function (): void {

    //Arrange
    /** @var CreateArticleService $createArticleService */
    $createArticleService = $this->app->make(CreateArticleService::class);
    $command = new CreateArticleCommand(
        title: 'Test Article',
        content: 'Test Article',
        excerpt: 'Test Article',
        slug: 'test-article',
        data: ['featured' => true]
    );

    //Act
    $articleId = $createArticleService->execute($command);

    //Assert
    expect($articleId)->toBeInstanceOf(ArticleId::class);

    assertDatabaseHas('articles', [
        'id' => $articleId->toString(),
        'title' => 'Test Article',
        'content' => 'Test Article',
        'excerpt' => 'Test Article',
        'slug' => 'test-article',
    ]);

});

test('creates unique slug when not provided', function (): void {
    //Arrange
    /** @var CreateArticleService $createArticleService */
    $createArticleService = $this->app->make(CreateArticleService::class);
    $command = new CreateArticleCommand(
        title : 'Test Article',
        content: 'Content',
        excerpt: 'Excerpt'
    );

    //Act
    $articleId = $createArticleService->execute($command);

    /** @var EloquentArticleRepository $repository */
    $repository = $this->app->make(EloquentArticleRepository::class);
    $article = $repository->findById($articleId);

    //Assert
    expect($article->getSlug()->toString())
        ->toBe('test-article');

    //Arrange part II
    $command2 = new CreateArticleCommand(
        title: 'Test Article',
        content: 'Different Content',
        excerpt: 'Different Excerpt',
    );

    $articleIdPartII = $createArticleService->execute($command2);
    $articleII = $repository->findById($articleIdPartII);

    expect($articleII->getSlug())
        ->not
        ->toBe('test-article')
        ->and($articleII->getSlug()->toString())
        ->toStartWith('test-article-1');

    // Create third article with same title
    $command3 = new CreateArticleCommand(
        title: 'Test Article',
        content: 'Content 3',
        excerpt: 'Excerpt 3'
    );
    $articleIdIII = $createArticleService->execute($command3);

    $articleIII = $repository->findById($articleIdIII);

    expect($articleIII->getSlug()->toString())
        ->toBe('test-article-2');

});

test('throws exception for empty title', function (): void {
    /** @var CreateArticleService $createArticleService */
    $createArticleService = $this->app->make(CreateArticleService::class);

    $command = new CreateArticleCommand(
        title: '',
        content: 'Content',
        excerpt: 'Excerpt'
    );

    $this->expectException(InvalidArgumentException::class);

    $createArticleService->execute($command);

});