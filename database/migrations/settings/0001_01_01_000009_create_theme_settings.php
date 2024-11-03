<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('theme.name', 'Bdynamic');
        $this->migrator->add('theme.about', 'The about BDynamic Theme!');
        $this->migrator->add('theme.author_name', 'Francisco Barrento');
        $this->migrator->add('theme.author_url', 'https://github.com/fbarrento');
        $this->migrator->add('theme.author_email', 'info@fbarrento.com');
    }
};
