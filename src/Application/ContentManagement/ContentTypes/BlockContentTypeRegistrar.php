<?php

namespace WordSphere\Core\Application\ContentManagement\ContentTypes;

use WordSphere\Core\Application\Types\Registrars\BaseTypeRegistrar;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\Types\Enums\RelationType;
use WordSphere\Core\Domain\Types\ValueObjects\CustomField;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;
use WordSphere\Core\Interfaces\Filament\Enums\FieldType;
use WordSphere\Core\Interfaces\Filament\Types\FilamentTypeData;

use function __;

class BlockContentTypeRegistrar extends BaseTypeRegistrar
{
    public function register(): void
    {
        $type = $this->registry->register(
            key: TypeKey::fromString('blocks'),
            entityClass: Content::class,
            interfaceData: (new FilamentTypeData(
                singularName: __('Block'),
                pluralName: __('Blocks'),
                navigationGroup: 'Content',
                icon: 'heroicon-o-square-3-stack-3d',
                description: __('Content block to be linked pages or other type of contents.'),
                hasAuthor: false,
                navigationOrder: 999,
            ))->toArray()
        );

        $this->addRelation(
            sourceType: $type,
            name: 'pages',
            targetTypeKey: TypeKey::fromString('pages'),
            relationType: RelationType::MANY_TO_MANY,
            inverseRelationName: 'blocks'
        );


    }
}
