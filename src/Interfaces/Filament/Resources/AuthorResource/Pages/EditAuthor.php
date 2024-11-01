<?php

namespace WordSphere\Core\Interfaces\Filament\Resources\AuthorResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Auth\AuthManager;
use Illuminate\Database\Eloquent\Model;
use WordSphere\Core\Application\ContentManagement\Commands\UpdateAuthorCommand;
use WordSphere\Core\Application\ContentManagement\Services\UpdateAuthorService;
use WordSphere\Core\Domain\ContentManagement\Repositories\AuthorRepositoryInterface;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Adapters\AuthorAdapter;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentAuthor;
use WordSphere\Core\Infrastructure\Identity\Persistence\UserModel;
use WordSphere\Core\Interfaces\Filament\Resources\AuthorResource;

class EditAuthor extends EditRecord
{
    protected static string $resource = AuthorResource::class;

    private AuthorRepositoryInterface $repository;

    private UpdateAuthorService $updateAuthorService;

    private AuthManager $auth;

    public function boot(
        AuthorRepositoryInterface $repository,
        UpdateAuthorService $updateAuthorService,
        AuthManager $auth
    ): void {
        $this->repository = $repository;
        $this->updateAuthorService = $updateAuthorService;
        $this->auth = $auth;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model|EloquentAuthor $record, array $data): EloquentAuthor
    {
        /** @var UserModel $user */
        $user = $this->auth->user();

        /** @var EloquentAuthor $record */
        $command = new UpdateAuthorCommand(
            id: Uuid::fromString($record->id),
            updatedBy: Uuid::fromString($user->uuid),
            name: $this->getData('name', $data),
            email: $this->getData('email', $data),
            bio: $this->getData('bio', $data),
            website: $this->getData('website', $data),
            photo: $this->getData('photo', $data),
            socialLinks: $this->getData('social_links', $data)
        );

        $this->updateAuthorService->execute($command);

        $domainAuthor = $this->repository->findById(Uuid::fromString($record->id));

        return AuthorAdapter::toEloquent($domainAuthor);
    }

    private function getData(string $key, array $data): mixed
    {
        if (! array_key_exists($key, $data)) {
            return null;
        }

        return $data[$key];
    }
}
