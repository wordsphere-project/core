<?php

use WordSphere\Core\Application\Factories\ContentManagement\AuthorFactory;
use WordSphere\Core\Domain\ContentManagement\Entities\Author;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\AuthorId;
use WordSphere\Core\Domain\Identity\ValueObjects\UserUuid;
use WordSphere\Core\Domain\MediaManagement\ValueObjects\Id;

test('can create an author with al properties', function (): void {
    $authorId = AuthorId::generate();
    $createdBy = UserUuid::generate();
    $featuredImage = Id::fromInt(0);
    $author = new Author(
        id: $authorId,
        name: 'Francisco B.',
        email: 'francisco.b@example.com',
        creator: $createdBy,
        bio: 'A passionate laravel developer',
        website: 'https://pinkary.com',
        featuredImage: $featuredImage,
        socialLinks: ['twitter' => 'francisco.b', 'facebook' => 'francisco.b']
    );

    expect($author)
        ->toBeInstanceOf(Author::class)
        ->and($author->getId())->toBe($authorId)
        ->and($author->getName())->toBe('Francisco B.')
        ->and($author->getEmail())->toBe('francisco.b@example.com')
        ->and($author->getBio())->toBe('A passionate laravel developer')
        ->and($author->getWebsite())->toBe('https://pinkary.com')
        ->and($author->getSocialLinks())->toBe(['twitter' => 'francisco.b', 'facebook' => 'francisco.b'])
        ->and($author->getFeaturedImage())->toBe($featuredImage)
        ->and($author->getCreatedBy())->toBe($createdBy)
        ->and($author->getUpdatedBy())->toBe($createdBy)
        ->and($author->getCreatedAt())->toBeInstanceOf(DateTimeImmutable::class)
        ->and($author->getUpdatedAt())->toBeInstanceOf(DateTimeImmutable::class);

});

test('can update author and track changes', function (): void {
    $updatedBy = UserUuid::generate();
    $author = AuthorFactory::new()
        ->make();

    $author->updateName('John Doe', $updatedBy);
    $author->updateEmail('john.doe@example.com', $updatedBy);

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
    $author = new Author(
        id: AuthorId::generate(),
        name: 'Francisco B.',
        email: 'francisco.b@example.com',
        creator: UserUuid::generate(),
    );

    expect($author)->toBeInstanceOf(Author::class)
        ->and($author->getBio())->toBeNull()
        ->and($author->getWebsite())->toBeNull()
        ->and($author->getSocialLinks())->toBe([]);
});

test('can update author bio', function (): void {
    $createdBy = UserUuid::generate();
    $author = new Author(
        id: AuthorId::generate(),
        name: 'Francisco B.',
        email: 'francisco.b@example.com',
        creator: $createdBy,
    );

    $author->updateBio('An experience journalist', $createdBy);

    expect($author->getBio())
        ->toBe('An experience journalist');
});

test('can update social links', function (): void {
    $author = AuthorFactory::new()
        ->make();

    $author->updateSocialLinks(
        ['twitter' => 'francisco.b', 'facebook' => 'francisco.b'],
        $author->getCreatedBy()
    );

    expect($author->getSocialLinks())
        ->toBe([
            'twitter' => 'francisco.b',
            'facebook' => 'francisco.b',
        ]);
});

test('can add a single social link', function (): void {
    $author = AuthorFactory::new()
        ->make();

    $author->addSocialLink('twitter', 'francisco.b', $author->getCreatedBy());
    $author->addSocialLink('facebook', 'francisco.b', $author->getCreatedBy());

    expect($author->getSocialLinks())
        ->toBe(['twitter' => 'francisco.b', 'facebook' => 'francisco.b']);

});

test('can remove a social link', function (): void {
    $author = AuthorFactory::new()
        ->make();

    $author->addSocialLink('twitter', 'francisco.b', $author->getCreatedBy());
    $author->removeSocialLink('twitter', $author->getCreatedBy());

    expect($author->getSocialLinks())->toBe([]);
});
