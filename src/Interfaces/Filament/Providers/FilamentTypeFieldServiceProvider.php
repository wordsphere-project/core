<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Providers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\ServiceProvider;
use WordSphere\Core\Domain\Types\ValueObjects\CustomField;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\ContentModel;
use WordSphere\Core\Infrastructure\Types\Services\TenantProjectProvider;
use WordSphere\Core\Interfaces\Filament\Enums\FieldType;
use WordSphere\Core\Interfaces\Filament\Types\TypeFieldRegistry;

class FilamentTypeFieldServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $registry = $this->app->make(TypeFieldRegistry::class);

        $registry->registerFieldRenderer(
            fieldType: FieldType::TEXT->value,
            renderer: function (CustomField $customField) {
                $config = $customField->getConfig();

                return TextInput::make($customField->getKey())
                    ->label($config['label'])
                    ->required($config['required'] ?? false);
            }
        );

        $registry->registerFieldRenderer(FieldType::RICH_EDITOR->value, function (CustomField $customField): RichEditor {
            $config = $customField->getConfig();

            return RichEditor::make($customField->getKey())
                ->label($config['label'])
                ->required($config['required'] ?? false)
                ->toolbarButtons($config['toolbar'] ?? []);
        });

        $registry->registerFieldRenderer(FieldType::URL->value, function (CustomField $customField): TextInput {
            $config = $customField->getConfig();

            return TextInput::make($customField->getKey())
                ->label($config['label'])
                ->required($config['required'] ?? false)
                ->columnSpan($config['columnSpan'] ?? [])
                ->url()
                ->prefixIcon('heroicon-o-globe-alt');
        });

        $registry->registerFieldRenderer(FieldType::BLOCKS->value, function (CustomField $customField): Select {
            $config = $customField->getConfig();

            return Select::make($customField->getKey())
                ->multiple()
                ->options(function (TenantProjectProvider $tenantProjectProvider) {
                    return ContentModel::query()
                        ->where('type', 'blocks')
                        ->where('tenant_id', $tenantProjectProvider->getCurrentTenantId())
                        ->where('project_id', $tenantProjectProvider->getCurrentProjectId())
                        ->pluck('slug', 'id')->all();
                })
                ->preload()
                ->searchable();
        });

        $registry->registerFieldRenderer(FieldType::DATE_PICKER->value, function (CustomField $customField): DatePicker {
            $config = $customField->getConfig();

            return DatePicker::make($customField->getKey())
                ->label($config['label'])
                ->columnSpan($config['columnSpan'] ?? [])
                ->prefixIcon('heroicon-o-calendar-days')
                ->required($config['required'] ?? false)
                ->native(false)
                ->format('Y-m-d');
        });

    }
}
