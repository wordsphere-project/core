<?php

declare(strict_types=1);

namespace WordSphere\Core\Legacy\Settings;

use Spatie\LaravelSettings\Settings;

class AppSettings extends Settings
{
    public string $name;

    public string $about;

    public bool $active;

    public string $theme;

    public string $timezone;

    public string $locale;

    public static function group(): string
    {
        return 'app';
    }
}
