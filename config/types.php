<?php

use WordSphere\Core\Application\ContentManagement\ContentTypes\BlockContentTypeRegistrar;
use WordSphere\Core\Application\ContentManagement\ContentTypes\EventContentTypeRegistrar;
use WordSphere\Core\Application\ContentManagement\ContentTypes\NewsArticleContentTypeRegistrar;
use WordSphere\Core\Application\ContentManagement\ContentTypes\PageContentTypeRegistrar;

return [
    /*
    |--------------------------------------------------------------------------
    | Content Type Registrars
    |--------------------------------------------------------------------------
    |
    | Here you can register all your content type providers. These providers
    | will be automatically loaded during boot to register content types.
    |
    */

    'registrars' => [
        //BlogPostContentTypeRegistrar::class,
        //NewsArticleContentTypeRegistrar::class,
        //PressReleaseContentTypeRegistrar::class,
        BlockContentTypeRegistrar::class,
        PageContentTypeRegistrar::class,
        NewsArticleContentTypeRegistrar::class,
        EventContentTypeRegistrar::class,
    ],
];
