<?php

namespace WordSphere\Core\Application\ContentManagement\ContentTypes;

use WordSphere\Core\Domain\ContentManagement\Support\BaseContentTypeRegistrar;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ContentType;

class PageContentTypeRegistrar extends BaseContentTypeRegistrar
{
    public function register(): void
    {
        $this->registry->register(
            contentType: new ContentType(
                key: 'pages',
                singularName: __('Page'),
                pluralName: __('Pages'),
                navigationGroup: 'Pages',
                description: __('Static pages for your website like About Us, Contact, Terms of Service, etc.'),
                icon: 'heroicon-o-document-duplicate',
            )
        );
    }
}
