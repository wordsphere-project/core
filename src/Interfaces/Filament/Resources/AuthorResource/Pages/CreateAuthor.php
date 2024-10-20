<?php

namespace WordSphere\Core\Interfaces\Filament\Resources\AuthorResource\Pages;

use Illuminate\Auth\AuthManager;
use WordSphere\Core\Application\ContentManagement\Commands\CreateAuthorCommand;
use WordSphere\Core\Application\ContentManagement\Services\CreateAuthorService;
use WordSphere\Core\Domain\ContentManagement\Repositories\AuthorRepositoryInterface;
use WordSphere\Core\Domain\Shared\ValueObjects\Id;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Adapters\AuthorAdapter;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\EloquentAuthorRepository;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentAuthor;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;
use WordSphere\Core\Interfaces\Filament\Resources\AuthorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAuthor extends CreateRecord
{
    protected static string $resource = AuthorResource::class;

    private AuthorRepositoryInterface $repository;
    private CreateAuthorService $createAuthorService;
    private AuthManager $auth;

    public function boot(
        AuthorRepositoryInterface $repository,
        CreateAuthorService $createAuthorService,
        AuthManager $auth
    ): void {
        $this->repository = $repository;
        $this->auth = $auth;
        $this->createAuthorService = $createAuthorService;
    }

    protected function handleRecordCreation(array $data): EloquentAuthor
    {

        /** @var EloquentUser $user */
        $user = $this->auth->user();

        $command = new CreateAuthorCommand(
            name: $this->getData('name', $data),
            email: $this->getData('email', $data),
            createdBy: Uuid::fromString($user->uuid),
            bio: $this->getData('bio', $data),
            website: $this->getData('website', $data),
            socialLinks: $this->getData('socialLinks', $data),
            featuredImage: $this->getData('featuredImage', $data) ?  Id::fromInt($this->getData('featuredImage', $data)): null,
        );

        $authorId = $this->createAuthorService->execute($command);

        $domainAuthor = $this->repository->findById($authorId);

        return AuthorAdapter::toEloquent($domainAuthor);

    }

    private function getData(string $key, array $data): mixed
    {
        if (!array_key_exists($key, $data)) {
            return null;
        }

        return $data[$key];
    }

}
