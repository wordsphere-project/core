<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Filament\Resources\ContentResource;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Str;
use WordSphere\Core\Application\ContentManagement\Services\ContentStatusServiceFactory;
use WordSphere\Core\Domain\ContentManagement\Entities\Content;
use WordSphere\Core\Domain\ContentManagement\Enums\ContentStatus;
use WordSphere\Core\Domain\Types\Entities\Type;
use WordSphere\Core\Domain\Types\Enums\RelationType;
use WordSphere\Core\Domain\Types\ValueObjects\AllowedRelation;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\ContentModel;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\ContentModel as EloquentArticle;
use WordSphere\Core\Infrastructure\Types\Services\TenantProjectProvider;
use WordSphere\Core\Interfaces\Filament\Concerns\InteractsWithStatus;

use function __;

class ContentForm
{
    use InteractsWithStatus;

    protected static function getEntityClass(): string
    {
        return Content::class;
    }

    public static function make(Form $form, Type $type): array
    {

        return [
            Split::make([
                Tabs::make('Main Content')
                    ->tabs([
                        Tab::make('General')
                            ->label(__('General'))
                            ->schema(
                                components: [
                                    Hidden::make('type')
                                        ->default($type->getKey()->toString()),
                                    TextInput::make('title')
                                        ->columnSpan(2)
                                        ->required(),
                                    TextInput::make('slug')
                                        ->columnSpan(2)
                                        ->required(),
                                    Textarea::make('excerpt')
                                        ->columnSpan(2),
                                    RichEditor::make('content')
                                        ->columnSpan(2),
                                    Select::make('custom_fields.blocks')
                                        ->multiple()
                                        ->options(function (TenantProjectProvider $tenantProjectProvider) {
                                            return ContentModel::query()
                                                ->where('type', 'blocks')
                                                ->where('tenant_id', $tenantProjectProvider->getCurrentTenantId())
                                                ->where('project_id', $tenantProjectProvider->getCurrentProjectId())
                                                ->pluck('slug', 'id')->all();
                                        })
                                        ->preload()
                                        ->searchable(),
                                ]
                            ),

                        Tab::make('Media')
                            ->schema([
                                CuratorPicker::make('media_ids')
                                    ->label('Media Gallery')
                                    ->multiple()
                                    ->relationship('media', 'id')
                                    ->buttonLabel('Add Media')
                                    ->columnSpanFull()
                                    ->acceptedFileTypes(['image/*'])
                                    ->helperText('Add images to the media gallery')
                                    ->orderColumn('order')
                                    ->reactive(),
                            ]),
                    ]),

                Group::make()
                    ->schema([
                        Section::make(__('Publish'))
                            ->schema(
                                components: [
                                    Placeholder::make('status')
                                        ->label(__('status.label'))
                                        ->content(fn (EloquentArticle $record) => $record->status)
                                        ->visible(fn (): bool => $form->getRecord() !== null),
                                    Select::make('visibility')
                                        ->options([
                                            'public' => 'Public',
                                            'private' => 'Private',
                                        ])
                                        ->label('Visibility'),
                                    DateTimePicker::make('published_at')
                                        ->label('Publish at')
                                        ->reactive(),
                                    Actions::make([
                                        Actions\Action::make('publish')
                                            ->label('Publish')
                                            ->action(function (EloquentArticle $record, ContentStatusServiceFactory $serviceFactory, AuthManager $authManager, Get $get, Set $set): void {
                                                static::publishContent($record, $serviceFactory, $authManager, $get, $set);
                                            })
                                            ->visible(fn (?EloquentArticle $record): bool => $record?->status === ContentStatus::DRAFT->toString()
                                            )
                                            ->keyBindings(['command+p', 'ctrl+p'])
                                            ->color('success'),

                                        Actions\Action::make('unpublish')
                                            ->label('Unpublish')
                                            ->action(function (EloquentArticle $record, ContentStatusServiceFactory $serviceFactory, AuthManager $authManager, Get $get, Set $set): void {
                                                static::unpublishContent($record, $serviceFactory, $authManager, $get, $set);
                                            })
                                            ->visible(fn (?EloquentArticle $record): bool => $record?->status === ContentStatus::PUBLISHED->toString()
                                            )
                                            ->keyBindings(['command+u', 'ctrl+u'])
                                            ->color('danger'),
                                    ]),
                                    Select::make('author_id')
                                        ->relationship('author', 'name')
                                        ->searchable(),
                                ]
                            )
                            ->collapsible(),
                        Section::make()
                            ->heading(__('Featured image'))
                            ->schema(
                                components: [
                                    CuratorPicker::make('featured_image_id')
                                        ->hiddenLabel()
                                        ->extraAttributes(['class' => 'max-w-xs']),
                                ]
                            ),

                    ])
                    ->grow(false)
                    ->columns(['md' => 2, 'lg' => 1])
                    ->extraAttributes(['class' => 'min-w-80 lg:max-w-xs']),

            ])
                ->columns(1)
                ->from('lg'),

        ];

    }

    protected static function buildRelationFields(Type $type): array
    {
        $fields = [];

        foreach ($type->getAllowedRelations() as $relation) {
            $field = match ($relation->getRelationType()) {
                RelationType::MANY_TO_MANY => static::buildManyToMAnyField($relation),
                default => null
            };

            if ($field) {
                $fields[] = $field;
            }

        }

        return $fields;
    }

    protected static function buildManyToMAnyField(AllowedRelation $relation): Select
    {
        $tenantProjectProvider = app(TenantProjectProvider::class);

        $field = Select::make($relation->getName())
            ->relationship(
                name: $relation->getName(),
                titleAttribute: 'title',
                modifyQueryUsing: fn ($query) => $query
                    ->where('tenant_id', $tenantProjectProvider->getCurrentTenantId()->toString())
                    ->where('project_id', $tenantProjectProvider->getCurrentProjectId()->toString())
                    ->where('type', $relation->getTargetType()->getKey()->toString())

            )
            ->multiple()
            ->preload()
            ->searchable()
            ->label(Str::title($relation->getName()))
            ->required($relation->isRequired());

        if ($relation->getMinItems()) {
            $field->minItems($relation->getMinItems());
        }

        if ($relation->getMaxItems()) {
            $field->maxItems($relation->getMaxItems());
        }

        if ($relation->isRequired()) {
            $field->required();
        }

        return $field;
    }
}
