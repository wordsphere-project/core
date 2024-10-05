<?php

declare(strict_types=1);

namespace WordSphere\Core\Filament\Resources\ArticleResource\Pages;

use WordSphere\Core\Filament\Resources\ArticleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;
}
