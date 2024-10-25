<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Builders;

use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;
use WordSphere\Core\Domain\ContentManagement\ContentTypeRegistry;

use function route;

readonly class ContentTypeNavigationBuilder
{
    public function __construct(
        private ContentTypeRegistry $contentTypeRegistry,
    ) {}

    public function build(NavigationBuilder $builder): NavigationBuilder
    {
        foreach ($this->contentTypeRegistry->all() as $contentType) {
            $builder->item(
                NavigationItem::make($contentType->pluralName)
                    ->icon($contentType->icon)
                    ->group($contentType->navigationGroup)
                    ->url(route('filament.wordsphere.resources.contents.index', ['contentType' => $contentType->key]))
            );
        }

        return $builder;
    }
}
