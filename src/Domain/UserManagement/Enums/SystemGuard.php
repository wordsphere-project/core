<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\UserManagement\Enums;

enum SystemGuard: string
{
    case WEB = 'web';
    case API = 'api';

}
