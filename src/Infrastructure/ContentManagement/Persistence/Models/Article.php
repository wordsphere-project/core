<?php

declare(strict_types=1);

namespace WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models;

use Carbon\Carbon;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use WordSphere\Core\Database\Factories\ArticleFactory;

/**
 * @property string $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string $excerpt
 * @property string $status
 * @property int $media_id
 * @property array $data
 * @property Carbon|DateTimeImmutable $deleted_at
 * @property Carbon|DateTimeImmutable $created_at
 * @property Carbon|DateTimeImmutable $updated_at
 * @property Carbon|DateTimeImmutable $published_at
 *
 * @method static ArticleFactory factory($count = null, $state = [])
 */
final class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

    use HasUuids;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'media_id',
    ];

    public function casts(): array
    {
        return [
            'data' => 'json',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            related: config('auth.providers.users.model')
        );
    }
}
