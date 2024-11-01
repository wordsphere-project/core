<?php

use WordSphere\Core\Domain\ContentManagement\Entities\Author;
use WordSphere\Core\Domain\ContentManagement\Repositories\AuthorRepositoryInterface;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Adapters\AuthorAdapter;
use WordSphere\Core\Infrastructure\Identity\Persistence\UserModel;
use WordSphere\Core\Interfaces\Filament\Resources\AuthorResource\Pages\EditAuthor;

use function WordSphere\Tests\livewire;

beforeEach(function (): void {
    $this->user = UserModel::factory()->create();
    $this->actingAs($this->user);
});

test('can update an author through Filament', function (): void {
    $author = new Author(
        Uuid::generate(),
        'Original Name',
        Uuid::fromString($this->user->uuid),
        Uuid::fromString($this->user->uuid),
        'original@email.com'
    );

    $eloquentAuthor = AuthorAdapter::toEloquent($author);
    $eloquentAuthor->save();

    $newData = [
        'name' => 'Updated Name',
        'email' => 'updated@email.com',
        'bio' => 'Updated bio',
    ];

    livewire(EditAuthor::class, ['record' => $eloquentAuthor->id])
        ->fillForm($newData)
        ->call('save')
        ->assertHasNoErrors();

    $updatedAuthor = app(AuthorRepositoryInterface::class)
        ->findById(Uuid::fromString($eloquentAuthor->id));

    expect($updatedAuthor->getName())->toBe('Updated Name')
        ->and($updatedAuthor->getEmail())->toBe('updated@email.com')
        ->and($updatedAuthor->getBio())->toBe('Updated bio');
});

test('validates author data in Filament edit form', function (): void {
    $author = new Author(
        Uuid::generate(),
        'Original Name',
        Uuid::fromString($this->user->uuid),
        Uuid::fromString($this->user->uuid),
        'original@email.com'
    );

    $eloquentAuthor = AuthorAdapter::toEloquent($author);
    $eloquentAuthor->save();

    $invalidData = [
        'name' => '', // Empty name should be invalid
        'email' => 'not-an-email', // Invalid email format
    ];

    livewire(EditAuthor::class, ['record' => $eloquentAuthor->id])
        ->fillForm($invalidData)
        ->call('save')
        ->assertHasErrors(['data.name', 'data.email']);
});
