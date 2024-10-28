<?php

namespace WordSphere\Core\Application\ContentManagement\ContentTypes;

use WordSphere\Core\Domain\ContentManagement\ContentTypeRegistry;
use WordSphere\Core\Domain\ContentManagement\Contracts\ContentTypeRegistrar;

abstract class BaseContentTypeRegistrar implements ContentTypeRegistrar
{
    public function __construct(
        protected ContentTypeRegistry $registry
    ) {}
}
