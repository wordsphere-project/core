<?php

declare(strict_types=1);

namespace WordSphere\Core\Enums;

enum ContentVisibility: int
{
    case PUBLIC = 0;
    case PRIVATE = 1;
    case MEMBERS_ONLY = 2;

}
