<?php

namespace WordSphere\Core\Application\ContentManagement\ContentTypes;

use WordSphere\Core\Domain\ContentManagement\ValueObjects\ContentType;

class PressReleaseContentTypeRegistrar extends BaseContentTypeRegistrar
{
    public function register(): void
    {
        $this->registry->register(
            contentType: new ContentType(
                key: 'press-releases',
                singularName: 'Press Release',
                pluralName: 'Press Releases',
                navigationGroup: 'Content',
                description: 'Official press releases and media communications',
                icon: 'heroicon-o-megaphone'
            )
        );
    }
}
