<?php

namespace WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use WordSphere\Core\Database\Factories\PageFactory;
use WordSphere\Core\Infrastructure\Support\Concerns\HasFeaturedImage;
use WordSphere\Core\Legacy\Enums\ContentStatus;
use WordSphere\Core\Legacy\Enums\ContentVisibility;

/**
 * @property int $id
 * @property string $uuid
 * @property string $title
 * @property string $path
 * @property string $content
 * @property string $excerpt
 * @property string $template
 * @property string $data
 * @property string $meta
 */
class EloquentPage extends Model
{
    /** @use HasFactory<PageFactory> */
    use HasFactory;

    use HasFeaturedImage;

    protected $table = 'pages';

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'status' => ContentStatus::class,
            'visibility' => ContentVisibility::class,
            'data' => 'json',
            'meta' => 'json',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (EloquentPage $model): void {
            $model->uuid = (string) Str::uuid();
        });
    }

    public static function newFactory(): PageFactory
    {
        return PageFactory::new();
    }
}
