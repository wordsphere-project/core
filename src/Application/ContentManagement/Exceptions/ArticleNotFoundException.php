<?php

namespace WordSphere\Core\Application\ContentManagement\Exceptions;

use Exception;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleUuid;

class ArticleNotFoundException extends Exception
{
    public function __construct(ArticleUuid $id)
    {
        parent::__construct($id);
    }
}
