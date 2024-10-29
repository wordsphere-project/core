<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Builders;

use Filament\Navigation\NavigationItem;
use WordSphere\Core\Domain\Types\TypeRegistry;

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
            $interfaceData = $type->getInterfaceData();
            $items[] = NavigationItem::make($interfaceData['pluralName'] ?? $type->getKey()->toString())
                ->icon($interfaceData['icon'] ?? 'heroicon-o-document')
                ->group($interfaceData['navigationGroup'] ?? '')
                ->url(route('filament.wordsphere.resources.contents.index', ['type' => $type->getKey()->toString()]))
                ->sort($interfaceData['navigationSort'] ?? 0);
        }

        return $items;
    }
}
