<?php

namespace WordSphere\Core\Interfaces\Filament\Resources\PageResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;
use WordSphere\Core\Interfaces\Filament\Resources\PageResource;
use function auth;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;


}
