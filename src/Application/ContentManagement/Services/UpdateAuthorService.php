<?php

declare(strict_types=1);

namespace WordSphere\Core\Application\ContentManagement\Services;

use WordSphere\Core\Application\ContentManagement\Commands\UpdateAuthorCommand;
use WordSphere\Core\Application\ContentManagement\Exceptions\AuthorNotFoundException;
use WordSphere\Core\Domain\ContentManagement\Repositories\AuthorRepositoryInterface;
use WordSphere\Core\Domain\Shared\Contracts\EventDispatcherInterface;

readonly class UpdateAuthorService
{
    public function __construct(
        private AuthorRepositoryInterface $authorRepository,
        private EventDispatcherInterface $eventDispatcher

    ) {}

    public function execute(UpdateAuthorCommand $command): void
    {

        $author = $this->authorRepository->findById($command->id);

        if (! $author) {
            throw new AuthorNotFoundException($command->id);
        }

        if ($command->name !== null) {
            $author->updateName($command->name);
        }

        if ($command->email !== null) {
            $author->updateEmail($command->email);
        }

        if ($command->bio !== null) {
            $author->updateBio($command->bio);
        }

        if ($command->website !== null) {
            $author->updateWebsite($command->website);
        }

        if ($command->photo !== null) {
            $author->updatePhoto($command->photo);
        }

        if ($command->socialLinks !== null) {
            $author->updateSocialLinks($command->socialLinks);
        }

        $author->setUpdatedBy($command->updatedBy);
        $author->finalizeUpdate();

        $this->authorRepository->save($author);

        foreach ($author->pullDomainEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }

    }
}
