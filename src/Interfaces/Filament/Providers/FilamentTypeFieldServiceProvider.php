<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Providers;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\ServiceProvider;
use WordSphere\Core\Interfaces\Filament\Enums\FilamentFieldType;
use WordSphere\Core\Interfaces\Filament\Types\TypeFieldRegistry;

class FilamentTypeFieldServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $registry = $this->app->make(TypeFieldRegistry::class);

        $registry->registerFieldRenderer(
            fieldType: FilamentFieldType::FILAMENT_TEXT->value,
            renderer: function (array $config) {
                return TextInput::make($config['key'])
                    ->label($config['label'])
                    ->required($config['required'] ?? false);
            }
        );

        $registry->registerFieldRenderer(FilamentFieldType::FILAMENT_RICH_EDITOR->value, function (array $config) {
            RichEditor::make($config['key'])
                ->label($config['label'])
                ->required($config['required'] ?? false)
                ->toolbarButtons($config['toolbar'] ?? []);
        });

    }
}
