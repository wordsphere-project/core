<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models;

use Awcodes\Curator\Models\Media as CuratorMedia;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use WordSphere\Core\Application\Factories\ContentManagement\MediaFactory;

/**
 * @property int $id
 * @property string $uuid
 * @property-read mixed $full_path
 * @property-read mixed $large_url
 * @property-read mixed $medium_url
 * @property-read mixed $pretty_name
 * @property-read mixed $resizable
 * @property-read mixed $size_for_humans
 * @property-read mixed $thumbnail_url
 * @property-read mixed $url
 *
 * @method static MediaFactory factory($count = null, $state = [])
 * @method static Builder<static>|EloquentMedia newModelQuery()
 * @method static Builder<static>|EloquentMedia newQuery()
 * @method static Builder<static>|EloquentMedia query()
 *
 * @mixin Eloquent
 */
class EloquentMedia extends CuratorMedia
{
    /** @use HasFactory<MediaFactory> */
    use HasFactory;

    protected $table = 'media';

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (EloquentMedia $media): void {
            $media->uuid = (string) Str::uuid();
        });
    }

    public static function newFactory(): MediaFactory
    {
        return MediaFactory::new();
    }
}
