<?php

use WordSphere\Core\Application\Factories\ContentManagement\AuthorFactory;
use WordSphere\Core\Domain\ContentManagement\Entities\Author as DomainAuthor;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

test('can create an author with al properties', function (): void {
    $authorId = Uuid::generate();
    $createdBy = Uuid::generate();
    $author = new DomainAuthor(
        id: $authorId,
        name: 'Francisco B.',
        createdBy: $createdBy,
        updatedBy: $createdBy,
        email: 'francisco.b@example.com',
        bio: 'A passionate laravel developer',
        website: 'https://pinkary.com',
        photo: 'profile-photo.jpg',
        socialLinks: ['twitter' => 'francisco.b', 'facebook' => 'francisco.b']
    );

    expect($author)
        ->toBeInstanceOf(DomainAuthor::class)
        ->and($author->getId())->toBe($authorId)
        ->and($author->getName())->toBe('Francisco B.')
        ->and($author->getEmail())->toBe('francisco.b@example.com')
        ->and($author->getBio())->toBe('A passionate laravel developer')
        ->and($author->getWebsite())->toBe('https://pinkary.com')
        ->and($author->getSocialLinks())->toBe(['twitter' => 'francisco.b', 'facebook' => 'francisco.b'])
        ->and($author->getPhoto())->toBe('profile-photo.jpg')
        ->and($author->getCreatedBy())->toBe($createdBy)
        ->and($author->getUpdatedBy())->toBe($createdBy)
        ->and($author->getCreatedAt())->toBeInstanceOf(DateTimeImmutable::class)
        ->and($author->getUpdatedAt())->toBeInstanceOf(DateTimeImmutable::class);

});

test('can update author and track changes', function (): void {
    $updatedBy = Uuid::generate();
    $author = AuthorFactory::new()
        ->makeForDomain([
            'updated_by' => $updatedBy,
        ]);

    $author->updateName('John Doe');
    $author->updateEmail('john.doe@example.com');

    expect($author->getName())
        ->toBe('John Doe')
        ->and($author->getEmail())
        ->toBe('john.doe@example.com')
        ->and($author->getUpdatedBy())
        ->toBe($updatedBy)
        ->and($author->getUpdatedAt())
        ->toBeGreaterThan($author->getCreatedAt());

});

test('can create an author without optional properties', function (): void {
    $author = new DomainAuthor(
        id: Uuid::generate(),
        name: 'Francisco B.',
        createdBy: Uuid::generate(),
        updatedBy: Uuid::generate(),
        email: 'francisco.b@example.com',
    );

    expect($author)->toBeInstanceOf(DomainAuthor::class)
        ->and($author->getBio())->toBeNull()
        ->and($author->getWebsite())->toBeNull()
        ->and($author->getSocialLinks())->toBe([]);
});

test('can update author bio', function (): void {
    $createdBy = Uuid::generate();
    $author = new DomainAuthor(
        id: Uuid::generate(),
        name: 'Francisco B.',
        createdBy: $createdBy,
        updatedBy: $createdBy,
        email: 'francisco.b@example.com',
    );

    $author->updateBio('An experience journalist');

    expect($author->getBio())
        ->toBe('An experience journalist');
});

test('can update social links', function (): void {
    $author = AuthorFactory::new()
        ->makeForDomain();

    $author->updateSocialLinks(
        ['twitter' => 'francisco.b', 'facebook' => 'francisco.b']
    );

    expect($author->getSocialLinks())
        ->toBe([
            'twitter' => 'francisco.b',
            'facebook' => 'francisco.b',
        ]);
});

test('can add a single social link', function (): void {
    $author = AuthorFactory::new()
        ->makeForDomain();

    $author->addSocialLink('twitter', 'francisco.b');
    $author->addSocialLink('facebook', 'francisco.b');

    expect($author->getSocialLinks())
        ->toBe(['twitter' => 'francisco.b', 'facebook' => 'francisco.b']);

});

test('can remove a social link', function (): void {
    $author = AuthorFactory::new()
        ->makeForDomain();

    $author->addSocialLink('twitter', 'francisco.b');
    $author->removeSocialLink('twitter');

    expect($author->getSocialLinks())->toBe([]);
});
