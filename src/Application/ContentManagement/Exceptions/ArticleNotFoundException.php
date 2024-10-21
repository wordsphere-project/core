<?php

namespace WordSphere\Core\Application\ContentManagement\Exceptions;

use Exception;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

class ArticleNotFoundException extends Exception
{
    public function __construct(Uuid $id)
    {
        parent::__construct($id);
    }
}
