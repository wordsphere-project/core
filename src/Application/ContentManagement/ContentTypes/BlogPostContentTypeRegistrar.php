<?php

namespace WordSphere\Core\Application\ContentManagement\ContentTypes;

use WordSphere\Core\Domain\ContentManagement\Support\BaseContentTypeRegistrar;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ContentType;

class BlogPostContentTypeRegistrar extends BaseContentTypeRegistrar
{
    public function register(): void
    {
        $this->registry->register(
            contentType: new ContentType(
                key: 'blog-posts',
                singularName: __('Post'),
                pluralName: __('Posts'),
                navigationGroup: 'Content',
                description: __('Regular blog post for the website'),
                icon: 'heroicon-o-document-text',
            )
        );
    }
}
