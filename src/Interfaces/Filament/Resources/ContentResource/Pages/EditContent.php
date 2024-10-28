<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Resources\ContentResource\Pages;

use Awcodes\Curator\Models\Media;
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
use WordSphere\Core\Infrastructure\ContentManagement\Adapters\MediaAdapter;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\ContentModel;
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

    protected function handleRecordUpdate(ContentModel|Model $record, array $data): ContentModel
    {

        if (! $record instanceof ContentModel) {
            throw new Exception(__('EloquentContent not found.'));
        }

        // Only process media if it's different from current media
        $media = [];
        if (! empty($record->media)) {
            $media = $record->media
                ->unique()
                ->values() // Reset array keys
                ->map(function ($media) {
                    /** @var Media $media */
                    return MediaAdapter::fromCurator($media);
                })
                ->toArray();
        }

        /** @var EloquentUser $updater */
        $updater = $this->auth->user();
        $updaterId = Uuid::fromString($updater->uuid);
        $articleId = Uuid::fromString($record->id);

        if (! isset($data['type'])) {
            $data['type'] = $record->type;
        }

        $command = new UpdateContentCommand(
            id: $articleId,
            type: $data['type'],
            updatedBy: $updaterId,
            title: $data['title'] ?? null,
            content: array_key_exists('content', $data) ? $data['content'] : null,
            excerpt: array_key_exists('excerpt', $data) ? $data['excerpt'] : null,
            slug: array_key_exists('slug', $data) ? $data['slug'] : null,
            customFields: array_key_exists('data', $data) ? $data['data'] : null,
            featuredImage: array_key_exists('featured_image_id', $data) ? ($data['featured_image_id'] ? Id::fromInt($data['featured_image_id']) : null) : null,
            media: $media,
        );

        $this->updateArticleService->execute($command);

        $updatedDomainArticle = $this->articleRepository->findByUuid($articleId);

        return ContentAdapter::toEloquent($updatedDomainArticle);
    }

    protected function getUpdatedValue(array $data, ContentModel $record, string $field, $default = null)
    {
        return array_key_exists($field, $data) ? $data[$field] : $record->{$field};
    }
}
