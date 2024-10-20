<?php

namespace WordSphere\Core\Interfaces\Filament\Resources\PageResource\Form;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use WordSphere\Core\Interfaces\Filament\Resources\PageResource;
use WordSphere\Core\Legacy\Contracts\CustomFieldsManagerContract;
use WordSphere\Core\Legacy\Enums\ResourceTab;
use WordSphere\Core\Legacy\Support\CustomFields\CustomFieldsScope;
use function __;

class FormCompiler
{
    public function compile(): Tabs
    {
        return Tabs::make('Tabs')
            ->tabs(fn (CustomFieldsManagerContract $customFieldsManager, Get $get): array => [
                $this->makeGeneralTab(),
                ...$customFieldsManager->getNonSystemScopeSchema(
                    resource: PageResource::class,
                    scope: new CustomFieldsScope(
                        key: 'template',
                        value: $get('template') ?? ''
                    )
                ),
                $this->makeSettingsTab(),
            ]);
    }

    protected function makeGeneralTab(): Tab
    {
        return Tab::make('general')
            ->columns(2)
            ->label(__('General'))
            ->schema(fn (CustomFieldsManagerContract $customFields, Get $get): array => [
                TextInput::make('title')
                    ->label(__('Title'))
                    ->maxLength(255)
                    ->columnSpan(2)
                    ->required(),

                TextInput::make('path')
                    ->label(__('Path'))
                    ->columnSpan(2)
                    ->required()
                    ->unique(
                        table: 'pages',
                        column: 'path',
                        ignoreRecord: true
                    ),

                Textarea::make('excerpt')
                    ->label(__('Excerpt'))
                    ->columnSpan(2)
                    ->visible(true)
                    ->reactive()
                    ->rows(4),

                RichEditor::make('content')
                    ->label(__('content.content'))
                    ->columnSpan(2)
                    ->visible(true)
                    ->reactive(),
                ...$customFields->getScopeSchema(
                    resource: PageResource::class,
                    tab: ResourceTab::GENERAL->value
                ),
                ...$customFields->getScopeSchema(
                    resource: PageResource::class,
                    tab: ResourceTab::SETTINGS->value,
                    scope: new CustomFieldsScope(
                        key: 'template',
                        value: $get('template') ?? ''
                    )
                ),
            ]
            );
    }

    protected function makeSettingsTab(): Tab
    {
        return Tab::make('settings')
            ->label(__('Settings'))
            ->schema(fn (CustomFieldsManagerContract $customFields, Get $get): array => [

                Section::make(__('Supported Features'))
                    ->statePath('meta.support')
                    ->schema(
                        components: [
                            Toggle::make('excerptSupport')
                                ->label(__('Excerpt'))
                                ->reactive(),
                            Toggle::make('contentSupport')
                                ->label(__('content.content'))
                                ->reactive(),
                        ]
                    ),
                ...$customFields->getScopeSchema(
                    resource: PageResource::class,
                    tab: ResourceTab::SETTINGS->value,
                ),
                ...$customFields->getScopeSchema(
                    resource: PageResource::class,
                    tab: ResourceTab::SETTINGS->value,
                    scope: new CustomFieldsScope(
                        key: 'template',
                        value: $get('template') ?? ''
                    )
                ),
            ]);
    }
}
