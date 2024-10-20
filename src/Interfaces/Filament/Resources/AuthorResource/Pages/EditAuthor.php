<?php

namespace WordSphere\Core\Interfaces\Filament\Resources\AuthorResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use WordSphere\Core\Interfaces\Filament\Resources\AuthorResource;

class EditAuthor extends EditRecord
{
    protected static string $resource = AuthorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
