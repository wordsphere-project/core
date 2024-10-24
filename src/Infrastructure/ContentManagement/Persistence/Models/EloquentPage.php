<?php

namespace WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models;

use Awcodes\Curator\Models\Media;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use WordSphere\Core\Database\Factories\PageFactory;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;
use WordSphere\Core\Infrastructure\Support\Concerns\CreatedUpdatedBy;
use WordSphere\Core\Infrastructure\Support\Concerns\HasFeaturedImage;
use WordSphere\Core\Legacy\Enums\ContentStatus;
use WordSphere\Core\Legacy\Enums\ContentVisibility;

/**
 * @property string $id
 * @property string $title
 * @property string $slug
 * @property string $path
 * @property string $created_by
 * @property string $updated_by
 * @property string $content
 * @property string $excerpt
 * @property array $custom_fields
 * @property string $template
 * @property int $sort_order
 * @property string $redirect_url
 * @property int $featured_image_id
 * @property EloquentMedia $featuredImage
 * @property ContentStatus $status
 * @property ContentVisibility $visibility
 * @property Carbon $publish_at
 * @property Carbon $published_at
 * @property Carbon $expires_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class EloquentPage extends Model
{
    use CreatedUpdatedBy;

    /** @use HasFactory<PageFactory> */
    use HasFactory;
    use HasUuids;
    use HasFeaturedImage;
    use CreatedUpdatedBy;

    protected $table = 'pages';

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'status' => ContentStatus::class,
            'visibility' => ContentVisibility::class,
            'custom_fields' => 'json',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(EloquentUser::class, 'created_by', 'uuid');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(EloquentUser::class, 'updated_by', 'uuid');
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    public static function newFactory(): PageFactory
    {
        return PageFactory::new();
    }
}
