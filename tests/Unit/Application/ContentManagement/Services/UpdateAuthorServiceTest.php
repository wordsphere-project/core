<?php

use WordSphere\Core\Application\ContentManagement\Commands\UpdateAuthorCommand;
use WordSphere\Core\Application\ContentManagement\Exceptions\AuthorNotFoundException;
use WordSphere\Core\Application\ContentManagement\Services\UpdateAuthorService;
use WordSphere\Core\Domain\ContentManagement\Entities\Author;
use WordSphere\Core\Domain\ContentManagement\Repositories\AuthorRepositoryInterface;
use WordSphere\Core\Domain\Shared\Contracts\EventDispatcherInterface;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

beforeEach(function (): void {
    $this->authorRepository = Mockery::mock(AuthorRepositoryInterface::class);
    $this->eventDispatcher = Mockery::mock(EventDispatcherInterface::class);
    $this->updateAuthorService = new UpdateAuthorService(
        $this->authorRepository,
        $this->eventDispatcher
    );
});

test('update author service updates author correctly', function (): void {
    $authorId = Uuid::generate();
    $updatedBy = Uuid::generate();

    $author = Mockery::mock(Author::class);
    $author->shouldReceive('updateName')->once()->with('New Name');
    $author->shouldReceive('updateEmail')->once()->with('new@email.com');
    $author->shouldReceive('updateBio')->once()->with('New bio');
    $author->shouldReceive('setUpdatedBy')->once()->with($updatedBy);
    $author->shouldReceive('finalizeUpdate')->once();
    $author->shouldReceive('pullDomainEvents')->once()->andReturn([]);

    $this->authorRepository->shouldReceive('findById')->with($authorId)->andReturn($author);
    $this->authorRepository->shouldReceive('save')->once()->with($author);

    $command = new UpdateAuthorCommand(
        id: $authorId,
        updatedBy: $updatedBy,
        name: 'New Name',
        email: 'new@email.com',
        bio: 'New bio'
    );

    $this->updateAuthorService->execute($command);
});

test('update author service throws exception when author not found', function (): void {
    $authorId = Uuid::generate();

    $this->authorRepository->shouldReceive('findById')->with($authorId)->andReturnNull();

    $command = new UpdateAuthorCommand(
        id: $authorId,
        updatedBy: Uuid::generate(),
        name: 'New Name'
    );

    $this->expectException(AuthorNotFoundException::class);

    $this->updateAuthorService->execute($command);
});
