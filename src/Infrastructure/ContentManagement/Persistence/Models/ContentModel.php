<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models;

use Awcodes\Curator\Models\Media;
use Carbon\Carbon;
use DateTimeImmutable;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder;
use WordSphere\Core\Database\Factories\ArticleFactory;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\Types\Contracts\TypeableInterface;
use WordSphere\Core\Domain\Types\TypeRegistry;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;
use WordSphere\Core\Infrastructure\Shared\Concerns\HasFeaturedImage;
use WordSphere\Core\Infrastructure\Shared\Concerns\HasType;
use WordSphere\Core\Infrastructure\Shared\Concerns\HasTypedRelations;
use WordSphere\Core\Infrastructure\Shared\Models\TenantProjectModel;

use function app;
use function array_key_exists;

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
 * @property string $tenant_id
 * @property string $project_id
 * @property null|Collection<Media>|Builder $media
 * @property Carbon|DateTimeImmutable $deleted_at
 * @property Carbon|DateTimeImmutable $created_at
 * @property Carbon|DateTimeImmutable $updated_at
 * @property Carbon|DateTimeImmutable $published_at
 * @property \Illuminate\Database\Eloquent\Relations\BelongsTo|\Illuminate\Database\Eloquent\Relations\BelongsToMany|\Illuminate\Database\Eloquent\Relations\HasMany|mixed $resource
 *
 * @method static ArticleFactory factory($count = null, $state = [])
 */
class ContentModel extends TenantProjectModel implements TypeableInterface
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

    use HasFeaturedImage;
    use HasType;
    use HasTypedRelations;
    use HasUuids;

    protected $table = 'contents';

    protected $fillable = [
        'id',
        'uuid',
        'type',
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
        'tenant_id',
        'project_id',
    ];

    public static function boot(): void
    {

        parent::boot();
        static::creating(function (ContentModel $model): void {
            if ($user = Filament::auth()->user()) {
                /** @var EloquentUser $user */
                $model->setAttribute('created_by', $user->uuid);
                $model->setAttribute('updated_by', $user->uuid);
            }
        });
        static::updating(function (ContentModel $model): void {
            if ($user = Filament::auth()->user()) {
                /** @var EloquentUser $user */
                $model->setAttribute('created_by', $model->created_by);
                $model->setAttribute('updated_by', $user->uuid);
            }
        });

    }

    public function casts(): array
    {
        return [
            'custom_fields' => 'json',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'tenant_id' => 'string',
            'project_id' => 'string',
            'type' => 'string',
        ];
    }

    public function getTypeEntityClass(): string
    {
        return Content::class;
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
        return $this->belongsTo(EloquentMedia::class, 'featured_image_id', 'id');
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'content_media', 'content_id', 'media_id')
            ->withPivot('order', 'attributes')
            ->orderBy('order');
    }

    public function __get($key)
    {

        // Check if this is a typed relationship
        if ($this->hasAttribute('type') && $this->getAttribute('type') !== null) {
            $typeRegistry = app(TypeRegistry::class);
            $type = $typeRegistry->get(TypeKey::fromString($this->getAttribute('type')));

            if ($type && array_key_exists($key, $type->getAllowedRelations())) {
                return $this->getRelationshipsByType($key, $type);
            }
        }

        return parent::__get($key);
    }

    public function __call($method, $parameters)
    {

        if (! method_exists($this, $method)) {
            if ($this->hasAttribute('type') && $this->getAttribute('type') !== null) {
                $typeRegistry = app(TypeRegistry::class);
                $typeKey = TypeKey::fromString($this->getAttribute('type'));
                $type = $typeRegistry->get($typeKey);

                if ($type && array_key_exists($method, $type->getAllowedRelations())) {
                    return $this->getRelationshipsByType($method, $type);
                }
            }
        }

        return parent::__call($method, $parameters);
    }
}
