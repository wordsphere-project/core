<?php

declare(strict_types=1);

namespace WordSphere\Core\Domain\UserManagement\Enums;

enum SystemRole: string
{
    case ADMIN = 'Admin';
    case DEVELOPER = 'Developer';
    case EDITOR = 'Editor';
    case SUPER_ADMIN = 'Super Admin';
    case USER = 'User';

}
