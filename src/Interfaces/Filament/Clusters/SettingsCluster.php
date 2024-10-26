<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Clusters;

class SettingsCluster
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 100;

    public static function getNavigationLabel(): string
    {
        return __('Settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return null; // Top level navigation item
    }

    public function getNavigationBadge(): ?string
    {
        return null;
    }

    public static function getSlug(): string
    {
        return 'settings';
    }
}
