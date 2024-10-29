<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Enums;

enum FilamentFieldType: string
{
    case FILAMENT_TEXT = 'filament:text';
    case FILAMENT_RICH_EDITOR = 'filament:rich_editor';
    case FILAMENT_IMAGE = 'filament:image';
    case FILAMENT_FILE = 'filament:file';
    case FILAMENT_FILES = 'filament:files';
    case FILAMENT_CHECKBOX = 'filament:checkbox';
    case FILAMENT_TEXTAREA = 'filament:textarea';
    case FILAMENT_REPEATER = 'repeater';

}
