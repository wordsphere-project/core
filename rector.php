<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Doctrine\CodeQuality\Rector\Property\TypedPropertyFromColumnTypeRector;
use Rector\Renaming\Rector\Name\RenameClassRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/config',
        __DIR__.'/resources',
        __DIR__.'/routes',
        __DIR__.'/scripts',
        __DIR__.'/src',
        __DIR__.'/tests',
        __DIR__.'/workbench',
    ])
    ->withRules([
        RenameClassRector::class,
    ])
    ->withConfiguredRule(
        RenameClassRector::class,
        []
    )
    ->withSkip([
        TypedPropertyFromColumnTypeRector::class => [
            __DIR__.'/src/**/Filament/Resources/**',
            __DIR__.'/src/**/Filament/Pages/**',
        ],
        InlineConstructorDefaultToPropertyRector::class => [
            __DIR__.'/src/**/Filament/Resources/**',
            __DIR__.'/src/**/Filament/Pages/**',
        ],
    ])
    ->withImportNames()
    ->withPreparedSets()
    ->withPhpVersion(80200)
    ->withTypeCoverageLevel(5);
