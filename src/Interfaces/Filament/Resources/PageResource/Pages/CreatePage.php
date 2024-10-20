<?php

namespace WordSphere\Core\Interfaces\Filament\Resources\PageResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Auth\AuthManager;
use Illuminate\Database\Eloquent\Model;
use WordSphere\Core\Application\ContentManagement\Commands\CreateAuthorCommand;
use WordSphere\Core\Application\ContentManagement\Services\CreateAuthorService;
use WordSphere\Core\Domain\Shared\ValueObjects\Id;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;
use WordSphere\Core\Infrastructure\ContentManagement\Adapters\AuthorAdapter;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentAuthor;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUser;
use WordSphere\Core\Infrastructure\Identity\Persistence\EloquentUserRepository;
use WordSphere\Core\Interfaces\Filament\Resources\PageResource;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;


}
