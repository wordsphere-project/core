<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\Repositories;

use WordSphere\Core\Domain\ContentManagement\Entities\Author;
use WordSphere\Core\Domain\Shared\ValueObjects\Email;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

interface AuthorRepositoryInterface
{
    public function nextIdentity(): Uuid;

    public function findById(Uuid $id): ?Author;

    public function save(Author $author): void;

    public function delete(Uuid $id): void;

    public function findByEmail(Email $email): ?Author;
}
