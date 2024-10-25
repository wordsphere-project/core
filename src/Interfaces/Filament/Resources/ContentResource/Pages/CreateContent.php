<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Resources\ContentResource\Pages;

use Awcodes\Curator\Models\Media;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Auth\AuthManager;
use WordSphere\Core\Application\ContentManagement\Commands\CreateContentCommand;
use WordSphere\Core\Application\ContentManagement\Services\CreateContentService;
use WordSphere\Core\Domain\ContentManagement\Repositories\ContentRepositoryInterface;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Adapters\ContentAdapter;
use WordSphere\Core\Infrastructure\ContentManagement\Adapters\MediaAdapter;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentContent;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentMedia;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;
use WordSphere\Core\Interfaces\Filament\Resources\ContentResource;

class CreateContent extends CreateRecord
{
    protected static string $resource = ContentResource::class;

    protected CreateContentService $createArticleService;

    protected ContentRepositoryInterface $articleRepository;

    protected AuthManager $auth;

    public function boot(
        CreateContentService $createArticleService,
        ContentRepositoryInterface $articleRepository,
        AuthManager $auth
    ): void {
        $this->createArticleService = $createArticleService;
        $this->articleRepository = $articleRepository;
        $this->auth = $auth;
    }


    public function handleRecordCreation(array $data): EloquentContent
    {
        /** @var EloquentUser $user */
        $user = $this->auth->user();

        // Convert curator media IDs to Media value objects
        $media = collect($data['media'] ?? [])
            ->map(function ($mediaId) {
                /** @var EloquentMedia $eloquentMedia */
                $eloquentMedia = Media::query()->find($mediaId);

                return MediaAdapter::fromCurator($eloquentMedia);
            })
            ->toArray();

        $command = new CreateContentCommand(
            type: $data['type'],
            createdBy: Uuid::fromString($user->uuid),
            title: $data['title'],
            content: $data['content'] ?? null,
            excerpt: $data['excerpt'] ?? null,
            slug: $data['slug'] ?? null,
            customFields: $data['data'] ?? null,
            featuredImage: $data['featured_image_id'],
            media: $media,
        );

        $articleUuid = $this->createArticleService->execute($command);

        $domainContent = $this->articleRepository->findByUuid($articleUuid);

        return ContentAdapter::toEloquent(
            domainContent: $domainContent,
        );

    }
}
