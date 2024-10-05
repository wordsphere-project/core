<?php

declare(strict_types=1);

namespace WordSphere\Core\Filament\Resources\ArticleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use WordSphere\Core\Filament\Resources\ArticleResource;

class ListArticles extends ListRecords
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
