<?php

namespace WordSphere\Core\Interfaces\Filament\Resources\PageResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use WordSphere\Core\Interfaces\Filament\Resources\PageResource;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;
}
