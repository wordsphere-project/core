<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\ContentManagement\Persistence;

use WordSphere\Core\Domain\ContentManagement\Entities\Author as DomainAuthor;
use WordSphere\Core\Domain\ContentManagement\Repositories\AuthorRepositoryInterface;
use WordSphere\Core\Domain\Shared\ValueObjects\Email;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Adapters\AuthorAdapter;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentAuthor;

class EloquentAuthorRepository implements AuthorRepositoryInterface
{
    public function nextIdentity(): Uuid
    {
        return Uuid::generate();
    }

    public function findById(Uuid $id): ?DomainAuthor
    {
        $eloquentAuthor = EloquentAuthor::query()->find($id);

        return $eloquentAuthor ? AuthorAdapter::toDomain($eloquentAuthor) : null;
    }

    public function save(DomainAuthor $author): void
    {
        $eloquentAuthor = EloquentAuthor::query()->findOrNew($author->getId()->toString());
        $this->updateModelFromEntity($eloquentAuthor, $author);
        $eloquentAuthor->save();
    }

    public function delete(Uuid $id): void
    {
        EloquentAuthor::destroy($id->toString());
    }

    public function findByEmail(Email $email): ?DomainAuthor
    {
        $eloquentAuthor = EloquentAuthor::query()
            ->where('email', $email->toString())
            ->first();

        return $eloquentAuthor ? AuthorAdapter::toDomain($eloquentAuthor) : null;
    }

    private function updateModelFromEntity(EloquentAuthor $eloquentAuthor, DomainAuthor $domainAuthor): void
    {

        $eloquentAuthor->id = $domainAuthor->getId()->toString();
        $eloquentAuthor->name = $domainAuthor->getName();
        $eloquentAuthor->email = $domainAuthor->getEmail();
        $eloquentAuthor->created_by = $domainAuthor->getCreatedBy()->toString();
        $eloquentAuthor->updated_by = $domainAuthor->getUpdatedBy()->toString();
        $eloquentAuthor->bio = $domainAuthor->getBio();
        $eloquentAuthor->website = $domainAuthor->getWebsite();
        $eloquentAuthor->featured_image_id = $domainAuthor->getFeaturedImage()->toInt();
        $eloquentAuthor->social_links = $domainAuthor->getSocialLinks();
        $eloquentAuthor->created_at = $domainAuthor->getCreatedAt();
        $eloquentAuthor->updated_at = $domainAuthor->getUpdatedAt();

    }
}
