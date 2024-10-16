<?php

namespace WordSphere\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use WordSphere\Core\Database\Factories\PageFactory;
use WordSphere\Core\Enums\ContentStatus;
use WordSphere\Core\Enums\ContentVisibility;

/**
 * @property int $id
 * @property string $title
 * @property string $path
 * @property string $content
 * @property string $excerpt
 * @property string $template
 * @property string $data
 * @property string $meta
 */
class Page extends Model
{
    /** @use HasFactory<PageFactory> */
    use HasFactory;

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

    public static function newFactory(): PageFactory
    {
        return PageFactory::new();
    }
}
