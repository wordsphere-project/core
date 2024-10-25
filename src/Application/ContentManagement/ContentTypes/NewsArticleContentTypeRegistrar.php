<?php

namespace WordSphere\Core\Application\ContentManagement\ContentTypes;

use WordSphere\Core\Domain\ContentManagement\Support\BaseContentTypeRegistrar;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ContentType;

class NewsArticleContentTypeRegistrar extends BaseContentTypeRegistrar
{
    public function register(): void
    {
        $this->registry->register(
            contentType: new ContentType(
                key: 'news',
                singularName: 'News Article',
                pluralName: 'News Articles',
                navigationGroup: 'Content',
                description: 'News articles and updates',
                icon: 'heroicon-o-newspaper'
            )
        );
    }
}
