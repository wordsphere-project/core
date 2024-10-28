<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Clusters;

use Filament\Clusters\Cluster;

class Settings extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 100;
}
