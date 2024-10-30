<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Enums;

enum FieldType: string
{
    case TEXT = 'text';
    case URL = 'url';
    case RICH_EDITOR = 'rich_editor';
    case IMAGE = 'image';
    case FILE = 'file';
    case FILES = 'files';
    case CHECKBOX = 'checkbox';
    case TEXTAREA = 'textarea';
    case REPEATER = 'repeater';

    case BLOCKS = 'blocks';

    case DATE_PICKER = 'date_picker';

}
