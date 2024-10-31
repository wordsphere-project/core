<?php

namespace WordSphere\Core\Application\ContentManagement\ContentTypes;

use WordSphere\Core\Application\Types\Registrars\BaseTypeRegistrar;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\Types\Enums\RelationType;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;
use WordSphere\Core\Interfaces\Filament\Enums\ContentFormPlaceholder;
use WordSphere\Core\Interfaces\Filament\Enums\FieldType;
use WordSphere\Core\Interfaces\Filament\Types\FilamentTypeData;

use function __;

class PageContentTypeRegistrar extends BaseTypeRegistrar
{
    public function register(): void
    {
        $type = $this->registry->register(
            key: TypeKey::fromString('pages'),
            entityClass: Content::class,
            interfaceData: (new FilamentTypeData(
                singularName: __('Page'),
                pluralName: __('Pages'),
                navigationGroup: __('content.content'),
                icon: 'heroicon-o-document-text',
                description: __('Static pages for your website such as About Us, Contact, Terms & Conditions, etc.'),
                hasAuthor: false,
                navigationOrder: 98
            ))->toArray()
        );

        //Relations
        $this->addRelation(
            sourceType: $type,
            name: 'blocks',
            targetTypeKey: TypeKey::fromString('blocks'),
            relationType: RelationType::MANY_TO_MANY,
            inverseRelationName: 'pages'
        );

        $this->addFields($type->getKey(), ContentFormPlaceholder::GENERAL_END->value, function () {
            return [
                [
                    'key' => 'custom_fields.blocks',
                    'type' => FieldType::BLOCKS->value,
                    'config' => [],
                ],
            ];
        });

    }
}
