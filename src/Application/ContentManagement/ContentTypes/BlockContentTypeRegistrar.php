<?php

namespace WordSphere\Core\Application\ContentManagement\ContentTypes;

use WordSphere\Core\Application\Types\Registrars\BaseTypeRegistrar;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\Types\Enums\RelationType;
use WordSphere\Core\Domain\Types\ValueObjects\CustomField;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;
use WordSphere\Core\Interfaces\Filament\Enums\FilamentFieldType;
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
                hasAuthor: false
            ))->toArray()
        );

        $this->addRelation(
            sourceType: $type,
            name: 'pages',
            targetTypeKey: TypeKey::fromString('pages'),
            relationType: RelationType::MANY_TO_MANY,
            inverseRelationName: 'blocks'
        );

        $this->addFields(
            typeKey: $type->getKey(),
            location: 'main',
            callback: fn () => [
                new CustomField(
                    key: 'custom_fields.subtitle',
                    type: FilamentFieldType::FILAMENT_TEXT->value,
                    config: [
                        'label' => __('Subtitle'),
                    ],
                    validation: [
                        'required' => false,
                        'max_length' => 255,
                    ]
                ),
            ]
        );

        $this->addFields(
            typeKey: $type->getKey(),
            location: 'blocks',
            callback: fn () => [
                new CustomField(
                    key: 'custom_fields.blocks',
                    type: FilamentFieldType::FILAMENT_REPEATER->value,
                    config: [
                        'label' => __('Content Blocks'),
                        'schema' => [
                            [
                                'name' => 'title',
                                'type' => FilamentFieldType::FILAMENT_TEXT->value,
                                'config' => [
                                    'label' => __('Title'),
                                    'required' => true,
                                    'maxLength' => 255,
                                ],
                            ],
                            [
                                'name' => 'content',
                                'type' => FilamentFieldType::FILAMENT_RICH_EDITOR->value,
                                'config' => [
                                    'label' => __('Content'),
                                    'required' => true,
                                ],
                            ],
                        ],
                        'reorderable' => true,
                        'collapsible' => true,
                        'itemLabel' => [
                            'from' => 'title',
                        ],
                    ],
                    validation: [
                        'required' => true,
                        'array' => true,
                    ]
                ),
            ]
        );

    }
}
