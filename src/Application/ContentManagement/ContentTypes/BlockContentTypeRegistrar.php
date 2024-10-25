<?php

namespace WordSphere\Core\Application\ContentManagement\ContentTypes;

use WordSphere\Core\Domain\ContentManagement\Support\BaseContentTypeRegistrar;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ContentType;

class BlockContentTypeRegistrar extends BaseContentTypeRegistrar
{
    public function register(): void
    {
        $this->registry->register(
            contentType: new ContentType(
                key: 'blocks',
                singularName: __('Block'),
                pluralName: __('Blocks'),
                navigationGroup: 'Pages',
                description: __('Content block to be linked pages or other type of contents'),
                icon: 'heroicon-o-square-3-stack-3d',
            )
        );
    }
}
