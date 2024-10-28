<?php

namespace WordSphere\Core\Interfaces\Filament\Clusters\Settings\Resources\TypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use WordSphere\Core\Interfaces\Filament\Clusters\Settings\Resources\TypeResource;

class EditType extends EditRecord
{
    protected static string $resource = TypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
