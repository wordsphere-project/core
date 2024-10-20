<?php

namespace WordSphere\Core\Interfaces\Filament\Resources\AuthorResource\Pages;

use WordSphere\Core\Interfaces\Filament\Resources\AuthorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAuthors extends ListRecords
{
    protected static string $resource = AuthorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
