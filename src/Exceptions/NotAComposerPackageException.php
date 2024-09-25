<?php

declare(strict_types=1);

namespace WordSphere\Core\Exceptions;

use Exception;

final class NotAComposerPackageException extends Exception
{
    public function __construct()
    {
        parent::__construct('It\'s not a composer package');
    }
}
