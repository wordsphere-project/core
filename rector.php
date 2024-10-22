<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Doctrine\CodeQuality\Rector\Property\TypedPropertyFromColumnTypeRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\TypeDeclaration\Rector\Class_\PropertyTypeFromStrictSetterGetterRector;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;

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
        InlineConstructorDefaultToPropertyRector::class,
        TypedPropertyFromColumnTypeRector::class,
        PropertyTypeFromStrictSetterGetterRector::class,
    ])
    ->withConfiguredRule(
        RenameClassRector::class,
        [
            Content::class => 'WordSphere\Core\Domain\ContentManagement\Entities\Content',
        ]
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
