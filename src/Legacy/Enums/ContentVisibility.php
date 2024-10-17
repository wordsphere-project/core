<?php

declare(strict_types=1);

namespace WordSphere\Core\Legacy\Enums;

enum ContentVisibility: int
{
    case PUBLIC = 0;
    case PRIVATE = 1;
    case MEMBERS_ONLY = 2;

}
