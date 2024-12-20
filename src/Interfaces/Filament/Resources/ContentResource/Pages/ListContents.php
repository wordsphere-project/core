<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Resources\ContentResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use WordSphere\Core\Interfaces\Filament\Resources\ContentResource;

class ListContents extends ListRecords
{
    protected static string $resource = ContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->url(fn (): string => static::getResource()::getUrl('create', [
                    'type' => request()->route('type'),
                ])),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index', [
            'type' => request()->route('type'),
        ]);
    }
}
