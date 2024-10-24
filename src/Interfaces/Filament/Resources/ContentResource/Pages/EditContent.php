<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Resources\ContentResource\Pages;

use Exception;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Auth\AuthManager;
use Illuminate\Database\Eloquent\Model;
use WordSphere\Core\Application\ContentManagement\Commands\UpdateContentCommand;
use WordSphere\Core\Application\ContentManagement\Services\UpdateContentService;
use WordSphere\Core\Domain\ContentManagement\Repositories\ContentRepositoryInterface;
use WordSphere\Core\Domain\Shared\ValueObjects\Id;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Adapters\ContentAdapter;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentContent as EloquentArticle;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;
use WordSphere\Core\Interfaces\Filament\Resources\ContentResource;

use function array_key_exists;

class EditContent extends EditRecord
{
    protected static string $resource = ContentResource::class;

    protected UpdateContentService $updateArticleService;

    protected ContentRepositoryInterface $articleRepository;

    protected AuthManager $auth;

    public function boot(
        UpdateContentService $updateArticleService,
        ContentRepositoryInterface $articleRepository,
        AuthManager $auth
    ): void {
        $this->updateArticleService = $updateArticleService;
        $this->articleRepository = $articleRepository;
        $this->auth = $auth;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(EloquentArticle|Model $record, array $data): EloquentArticle
    {

        if (! $record instanceof EloquentArticle) {
            throw new Exception(__('EloquentContent not found.'));
        }

        /** @var EloquentUser $updater */
        $updater = $this->auth->user();
        $updaterId = Uuid::fromString($updater->uuid);
        $articleId = Uuid::fromString($record->id);

        $command = new UpdateContentCommand(
            id: $articleId,
            updatedBy: $updaterId,
            title: $data['title'] ?? null,
            content: array_key_exists('content', $data) ? $data['content'] : null,
            excerpt: array_key_exists('excerpt', $data) ? $data['excerpt'] : null,
            slug: array_key_exists('slug', $data) ? $data['slug'] : null,
            customFields: array_key_exists('data', $data) ? $data['data'] : null,
            featuredImage: array_key_exists('featured_image_id', $data) ? ($data['featured_image_id'] ? Id::fromInt($data['featured_image_id']) : null) : null,
        );

        $this->updateArticleService->execute($command);

        $updatedDomainArticle = $this->articleRepository->findByUuid($articleId);

        return ContentAdapter::toEloquent($updatedDomainArticle);
    }

    protected function getUpdatedValue(array $data, EloquentArticle $record, string $field, $default = null)
    {
        return array_key_exists($field, $data) ? $data[$field] : $record->{$field};
    }
}
