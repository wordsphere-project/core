<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\ContentManagement\Exceptions;

use Exception;

class InvalidArticleStatusException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
