<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Services;

use WordSphere\Core\Application\ContentManagement\Commands\CreateAuthorCommand;
use WordSphere\Core\Domain\ContentManagement\Entities\Author;
use WordSphere\Core\Domain\ContentManagement\Repositories\AuthorRepositoryInterface;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

readonly class CreateAuthorService
{
    public function __construct(
        private AuthorRepositoryInterface $authorRepository
    ) {}

    public function execute(CreateAuthorCommand $command): Uuid
    {
        $author = new Author(
            id: $this->authorRepository->nextIdentity(),
            name: $command->name,
            createdBy: $command->createdBy,
            updatedBy: $command->createdBy,
            email: $command->email,
            bio: $command->bio,
            website: $command->website,
            photo: $command->photo,
            socialLinks: $command->socialLinks,
        );

        $this->authorRepository->save($author);

        return $author->getId();

    }
}
