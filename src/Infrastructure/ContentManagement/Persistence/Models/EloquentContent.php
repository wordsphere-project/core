<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models;

use Awcodes\Curator\Models\Media;
use Carbon\Carbon;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder;
use WordSphere\Core\Database\Factories\ArticleFactory;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;
use WordSphere\Core\Infrastructure\Support\Concerns\HasFeaturedImage;

/**
 * @property string $id
 * @property string $type
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string $excerpt
 * @property string $status
 * @property int $feature_image_id
 * @property array $custom_fields
 * @property string $created_by
 * @property string $updated_by
 * @property int $featured_image_id
 * @property null|Collection<Media>|Builder $media
 * @property Carbon|DateTimeImmutable $deleted_at
 * @property Carbon|DateTimeImmutable $created_at
 * @property Carbon|DateTimeImmutable $updated_at
 * @property Carbon|DateTimeImmutable $published_at
 *
 * @method static ArticleFactory factory($count = null, $state = [])
 */
class EloquentContent extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

    use HasFeaturedImage;
    use HasUuids;

    protected $table = 'contents';

    protected $fillable = [
        'id',
        'uuid',
        'title',
        'slug',
        'content',
        'excerpt',
        'custom_fields',
        'status',
        'author_id',
        'featured_image_id',
        'published_at',
        'created_by',
        'updated_by',
    ];

    public function casts(): array
    {
        return [
            'custom_fields' => 'json',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(EloquentUser::class, 'author_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(EloquentUser::class, 'created_by', 'uuid');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(EloquentUser::class, 'updated_by', 'uuid');
    }

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(EloquentMedia::class, 'featured_image_id', 'uuid');
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'content_media', 'content_id', 'media_id')
            ->withPivot('order', 'attributes')
            ->orderBy('order');
    }
}
