<?php

declare(strict_types=1);

namespace WordSphere\Core\Filament\Resources\ArticleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use WordSphere\Core\Filament\Resources\ArticleResource;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
