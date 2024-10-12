<?php

namespace WordSphere\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use WordSphere\Core\Database\Factories\PageFactory;
use WordSphere\Core\Enums\ContentStatus;
use WordSphere\Core\Enums\ContentVisibility;

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
        ];
    }
}
