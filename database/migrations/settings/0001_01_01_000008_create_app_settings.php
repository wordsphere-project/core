<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('app.name', 'WordSphere');
        $this->migrator->add('app.about', 'WordSphere Website');
        $this->migrator->add('app.active', true);
        $this->migrator->add('app.theme', 'wordsphere/orbit-theme');
        $this->migrator->add('app.timezone', 'UTC');
        $this->migrator->add('app.locale', 'en');
    }
};
