<?php

namespace Workbench\App\Themes\CustomFields;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\PathGenerators\DatePathGenerator;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Illuminate\Contracts\Container\BindingResolutionException;
use WordSphere\Core\Interfaces\Filament\Resources\PageResource;
use WordSphere\Core\Legacy\Contracts\CustomFieldsManagerContract;
use WordSphere\Core\Legacy\Enums\ResourceTab;
use WordSphere\Core\Legacy\Support\CustomFields\CustomFieldsScope;

use function __;
use function app;

readonly class HomePageCustomFields
{
    public function __construct(
        private CustomFieldsManagerContract $customFieldsManager,
    ) {}

    /**
     * @throws BindingResolutionException
     */
    public static function forGeneralTab(): void
    {

        /** @var HomePageCustomFields $self */
        $self = app()->make(self::class);

        $self->customFieldsManager->registerFields(
            resource: PageResource::class,
            tab: ResourceTab::GENERAL->value,
            fields: [
                Section::make(__('About us'))
                    ->statePath('custom_fields.about')
                    ->schema(
                        components: [
                            TextInput::make('title')
                                ->label(__('Title'))
                                ->columnSpan(2),

                            RichEditor::make('content')
                                ->label('About Us')
                                ->maxLength(500)
                                ->columnSpan(2),

                        ]
                    )->columns(2),
            ]
        );
    }

    public static function forAboutUsTab(): void
    {
        /** @var HomePageCustomFields $self */
        $self = app()->make(self::class);

        $self->customFieldsManager->registerFields(
            resource: PageResource::class,
            tab: 'about-us',
            scope: new CustomFieldsScope(
                key: 'template',
                value: 'home'
            ),
            fields: [
                Tab::make('about-us')
                    ->label(__('About us'))
                    ->statePath('custom_fields.about')
                    ->schema(
                        components: [
                            TextInput::make('title')
                                ->label(__('Title'))
                                ->columnSpan(2),

                            RichEditor::make('content')
                                ->label('About Us')
                                ->maxLength(500)
                                ->columnSpan(2),

                            TextInput::make('action')
                                ->label(__('Action'))
                                ->default('')
                                ->hint(__('The route path or route name')),

                            Select::make('target')
                                ->label(__('Target'))
                                ->default('_self')
                                ->options(
                                    options: [
                                        '_blank' => 'Blank',
                                        '_self' => 'Self',
                                        '_parent' => 'Parent',
                                        '_top' => 'Top',
                                    ]
                                ),

                            Repeater::make('slides')
                                ->label(__('Slides'))
                                ->columns(2)
                                ->schema(
                                    components: [
                                        TextInput::make('title')
                                            ->label(__('Title'))
                                            ->maxLength(45),
                                        TextInput::make('alt')
                                            ->label(__('Alt'))
                                            ->maxLength(45),
                                        CuratorPicker::make('media_id')
                                            ->label(__('Image'))
                                            ->buttonLabel(__('Add Image'))
                                            ->pathGenerator(DatePathGenerator::class)
                                            ->size('sm')
                                            ->listDisplay(true),
                                    ]
                                )
                                ->columnSpan(2),
                        ]
                    ),
            ]
        );

    }
}
