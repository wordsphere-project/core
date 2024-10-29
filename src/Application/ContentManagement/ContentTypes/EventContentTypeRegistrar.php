<?php

namespace WordSphere\Core\Application\ContentManagement\ContentTypes;

use WordSphere\Core\Application\Types\Registrars\BaseTypeRegistrar;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;
use WordSphere\Core\Interfaces\Filament\Types\FilamentTypeData;

use function __;

class EventContentTypeRegistrar extends BaseTypeRegistrar
{
    public function register(): void
    {
        $this->registry->register(
            key: TypeKey::fromString('events'),
            entityClass: Content::class,
            interfaceData: (new FilamentTypeData(
                singularName: __('content.event'),
                pluralName: __('content.events'),
                navigationGroup: __('content.content'),
                icon: 'heroicon-o-calendar-days',
                description: __('Content type for managing in-person and online events.'),
                hasAuthor: false,
                navigationOrder: 2
            ))->toArray()
        );
    }
}
