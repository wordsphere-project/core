<?php

declare(strict_types=1);

namespace App\Enums;

enum SystemGuard: string
{
    case WEB = 'web';
    case API = 'api';

}
