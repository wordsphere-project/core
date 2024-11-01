<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\Shared\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait HasUuidAttribute
{
    public static function bootHasUuidAttribute(): void
    {

        static::creating(function (Model $model): void {
            if (Schema::hasColumn($model->getTable(), 'uuid') && $model->getAttribute('uuid') === null) {
                /** @phpstan-ignore-next-line  */
                $model->setAttribute('uuid', $model->newUniqueId());
            }
        });

    }

    public function newUniqueId(): string
    {
        return (string) Str::orderedUuid();
    }
}
