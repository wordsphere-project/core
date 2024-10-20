<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models;

use Carbon\Carbon;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use WordSphere\Core\Application\Factories\ContentManagement\AuthorFactory;
use WordSphere\Core\Infrastructure\Shared\Concerns\HasFeaturedImage;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $bio
 * @property string $website
 * @property array $social_links
 * @property string $photo
 * @property string $created_by
 * @property Carbon|DateTimeImmutable $created_at
 * @property string $updated_by
 * @property Carbon|DateTimeImmutable $updated_at
 */
class EloquentAuthor extends Model
{
    /** @use HasFactory<AuthorFactory> */
    use HasFactory;

    use HasFeaturedImage;
    use HasUuids;

    protected $table = 'authors';

    protected $fillable = ['name', 'email', 'bio', 'website', 'social_links', 'featured_image_id', 'created_by', 'updated_by'];

    public function casts(): array
    {
        return [
            'social_links' => 'array',
        ];
    }

    protected static function newFactory(): AuthorFactory
    {
        return AuthorFactory::new();
    }
}
