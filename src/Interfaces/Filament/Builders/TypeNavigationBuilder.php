<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Builders;

use Filament\Navigation\NavigationItem;
use WordSphere\Core\Domain\Types\TypeRegistry;
use WordSphere\Core\Interfaces\Filament\Types\FilamentTypeData;

use function route;

readonly class TypeNavigationBuilder
{
    public function __construct(
        private TypeRegistry $registry,
    ) {}

    public function build(): array
    {
        $types = $this->registry->all();
        $items = [];

        foreach ($types as $type) {
            $interfaceData = FilamentTypeData::fromArray($type->getInterfaceData());
            $items[] = NavigationItem::make(label: ! empty($interfaceData->getPluralName()) ? $interfaceData->getPluralName() : $type->getKey()->toString())
                ->icon($interfaceData->getIcon() ?? 'heroicon-o-document')
                ->group($interfaceData->getNavigationGroup() ?? '')
                ->url(route('filament.wordsphere.resources.contents.index', ['type' => $type->getKey()->toString()]))
                ->sort($interfaceData->getNavigationOrder() ?? 10000);
        }

        return $items;
    }
}
