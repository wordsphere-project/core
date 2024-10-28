<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Shared\Concerns;

use Illuminate\Database\Eloquent\Model;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;

use function auth;

trait CreatedUpdatedBy
{
    public static function bootCreatedUpdatedBy(): void
    {
        /** @var EloquentUser $user */
        $user = auth()->user();

        static::creating(function (Model $model) use ($user): void {
            if (! $model->isDirty('created_by')) {
                /** @phpstan-ignore-next-line  */
                $model->created_by = $user->uuid;
            }

            if (! $model->isDirty('updated_by')) {
                /** @phpstan-ignore-next-line  */
                $model->updated_by = $user->uuid;
            }
        });

        static::updating(function (Model $model) use ($user): void {
            if (! $model->isDirty('updated_by')) {
                /** @phpstan-ignore-next-line  */
                $model->updated_by = $user->uuid;
            }
        });
    }
}
