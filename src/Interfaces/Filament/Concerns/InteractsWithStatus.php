<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Concerns;

use Exception;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Auth\AuthManager;
use Illuminate\Database\Eloquent\Model;
use WordSphere\Core\Application\ContentManagement\Commands\ChangeContentStatusCommand;
use WordSphere\Core\Application\ContentManagement\Services\ContentStatusServiceFactory;
use WordSphere\Core\Domain\ContentManagement\Enums\ContentStatus;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\ContentModel;
use WordSphere\Core\Infrastructure\Identity\Persistence\UserModel;

trait InteractsWithStatus
{
    public static function changeContentStatus(
        Model|ContentModel $record,
        ContentStatus $newStatus,
        ContentStatusServiceFactory $serviceFactory,
        AuthManager $auth,
        ?Get $get = null,
        ?Set $set = null
    ): void {

        /** @var UserModel $user */
        $user = $auth->user();

        if (! $record instanceof ContentModel) {
            return;
        }

        $command = new ChangeContentStatusCommand(
            id: $record->id,
            newStatus: $newStatus,
            statusChangedBy: Uuid::fromString($user->uuid)
        );

        try {

            $contentType = static::getEntityClass();
            $statusService = $serviceFactory->create($contentType);
            $statusService->execute($command);

            $record->refresh();

            if ($get && $set) {
                $set('status', $record->status);
                $set('published_at', $record->published_at);
            }

            Notification::make()
                ->title(__("Content status changed to {$newStatus->toString()} successfully"))
                ->success()
                ->send();

        } catch (Exception $exception) {

            Notification::make()
                ->title(__("Unable to change content status to {$newStatus->toString()}"))
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }

    }

    public static function publishContent(
        $record,
        ContentStatusServiceFactory $serviceFactory,
        AuthManager $auth,
        ?Get $get = null,
        ?Set $set = null
    ): void {
        static::changeContentStatus($record, ContentStatus::PUBLISHED, $serviceFactory, $auth, $get, $set);
    }

    public static function unpublishContent(
        $record,
        ContentStatusServiceFactory $serviceFactory,
        AuthManager $auth,
        ?Get $get = null,
        ?Set $set = null
    ): void {
        static::changeContentStatus($record, ContentStatus::DRAFT, $serviceFactory, $auth, $get, $set);
    }

    abstract protected static function getEntityClass(): string;
}
