<?php

namespace WordSphere\Core\Interfaces\Filament\Enums;

enum ContentFormPlaceholder: string
{
    case GENERAL_START = 'general-start';
    case GENERAL_BEFORE_CONTENT = 'general-before-content';
    case GENERAL_AFTER_CONTENT = 'general-after-content';

    case GENERAL_END = 'general-end';

}
