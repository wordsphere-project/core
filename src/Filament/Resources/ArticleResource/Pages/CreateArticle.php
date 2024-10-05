<?php

declare(strict_types=1);

namespace WordSphere\Core\Filament\Resources\ArticleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use WordSphere\Core\Filament\Resources\ArticleResource;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;
}
