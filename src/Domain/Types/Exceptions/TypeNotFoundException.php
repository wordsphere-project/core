<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\Types\Exceptions;

use Exception;
use WordSphere\Core\Domain\Shared\ValueObjects\Uuid;

class TypeNotFoundException extends Exception
{
    public function __construct(Uuid $type)
    {
        parent::__construct("Type '{$type}' not found");
    }
}
