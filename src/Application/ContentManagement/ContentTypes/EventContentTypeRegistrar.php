<?php

namespace WordSphere\Core\Application\ContentManagement\ContentTypes;

use WordSphere\Core\Application\Types\Registrars\BaseTypeRegistrar;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;
use WordSphere\Core\Interfaces\Filament\Enums\ContentFormPlaceholder;
use WordSphere\Core\Interfaces\Filament\Enums\FieldType;
use WordSphere\Core\Interfaces\Filament\Types\FilamentTypeData;

use function __;

class EventContentTypeRegistrar extends BaseTypeRegistrar
{
    public function register(): void
    {
        $type = $this->registry->register(
            key: TypeKey::fromString('events'),
            entityClass: Content::class,
            interfaceData: (new FilamentTypeData(
                singularName: __('content.event'),
                pluralName: __('content.events'),
                navigationGroup: __('content.content'),
                icon: 'heroicon-o-calendar-days',
                description: __('Content type for managing in-person and online events.'),
                showExcerpt: false,
                showContent: true,
                hasAuthor: false,
                navigationOrder: 2
            ))->toArray()
        );

        $this->addFields($type->getKey(), ContentFormPlaceholder::GENERAL_BEFORE_CONTENT->value, function () {
            return [
                [
                    'key' => 'custom_fields.url',
                    'type' => FieldType::URL->value,
                    'config' => [
                        'required' => false,
                        'label' => __('content.url'),
                        'columnSpan' => 1,
                    ],
                ],
                [
                    'key' => 'custom_fields.date',
                    'type' => FieldType::DATE_PICKER->value,
                    'config' => [
                        'label' => 'content.date',
                        'columnSpan' => 1,
                        'required' => true,
                    ],
                ],
            ];
        });

    }
}
