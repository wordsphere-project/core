<?php

namespace WordSphere\Core\Application\ContentManagement\Exceptions;

use Exception;
use WordSphere\Core\Domain\ContentManagement\ValueObjects\ArticleId;

class ArticleNotFoundException extends Exception
{
    public function __construct(ArticleId $id)
    {
        parent::__construct($id);
    }
}
