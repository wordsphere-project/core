<?php

namespace WordSphere\Core\Application\ContentManagement\Exceptions;

use Exception;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

class AuthorNotFoundException extends Exception
{
    public function __construct(Uuid $id)
    {
        parent::__construct("Author with ID {$id} not found.");
    }
}
