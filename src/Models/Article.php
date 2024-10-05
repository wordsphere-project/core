<?php

declare(strict_types=1);

namespace WordSphere\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use WordSphere\Core\Database\Factories\ArticleFactory;

final class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'media_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            related: config('auth.providers.users.model')
        );
    }
}
