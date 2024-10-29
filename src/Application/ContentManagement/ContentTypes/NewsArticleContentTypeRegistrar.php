<?php

namespace WordSphere\Core\Application\ContentManagement\ContentTypes;

use WordSphere\Core\Application\Types\Registrars\BaseTypeRegistrar;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\Types\ValueObjects\TypeKey;
use WordSphere\Core\Interfaces\Filament\Types\FilamentTypeData;

use function __;

class NewsArticleContentTypeRegistrar extends BaseTypeRegistrar
{
    public function register(): void
    {
        $this->registry->register(
            key: TypeKey::fromString('news'),
            entityClass: Content::class,
            interfaceData: (new FilamentTypeData(
                singularName: __('content.new'),
                pluralName: __('content.news'),
                navigationGroup: __('content.content'),
                icon: 'heroicon-o-newspaper',
                description: __('Content type used for publishing news, updates and announcements'),
                hasAuthor: false,
                navigationOrder: 1
            ))->toArray()
        );
    }
}
