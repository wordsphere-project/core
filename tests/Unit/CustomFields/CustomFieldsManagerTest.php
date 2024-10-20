<?php

declare(strict_types=1);

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Illuminate\Contracts\Container\BindingResolutionException;
use WordSphere\Core\Filament\Resources\PageResource;
use WordSphere\Core\Legacy\Contracts\CustomFieldsManagerContract;
use WordSphere\Core\Legacy\Enums\ResourceTab;
use WordSphere\Core\Legacy\Support\CustomFields\BaseResourceCustomFieldsScope;
use WordSphere\Core\Legacy\Support\CustomFields\CustomFieldsContainer;
use WordSphere\Core\Legacy\Support\CustomFields\CustomFieldsScope;
use WordSphere\Tests\Unit\CustomFields\TestInteractsCustomFieldsManager;

uses(TestInteractsCustomFieldsManager::class);

beforeEach(/**
 * @throws BindingResolutionException
 */ closure: function (): void {

    $this->manager = app()->make( // @phpstan-ignore-line
        abstract: CustomFieldsManagerContract::class
    );

});

describe('custom fields can be registered', function (): void {

    it('returns the non system tabs custom fields', function (): void {

        $fieldsForBaseScope = [
            TextInput::make('address')
                ->label(__('Address')),
        ];

        $this->manager->registerFields(
            resource: PageResource::class,
            tab: ResourceTab::GENERAL->value,
            fields: $fieldsForBaseScope,
        );

        $fieldsForAboutUsTab = [
            TextInput::make('about_us'),
        ];

        $fieldsForWhoWeAreTab = [
            TextInput::make('who_we_are'),
        ];

        $this->manager->registerFields(
            resource: PageResource::class,
            tab: 'who_we_are',
            scope: new CustomFieldsScope(
                key: 'template',
                value: 'home'
            ),
            fields: $fieldsForWhoWeAreTab,
        );

        $this->manager->registerFields(
            resource: PageResource::class,
            tab: 'about_us',
            scope: new CustomFieldsScope(
                key: 'template',
                value: 'home'
            ),
            fields: $fieldsForAboutUsTab,
        );

        $nonSystemScopeFields = $this->manager->getNonSystemScopeSchema(
            resource: PageResource::class,
            scope: new CustomFieldsScope(
                key: 'template',
                value: 'home'
            )
        );

        expect($nonSystemScopeFields)
            ->toHaveCount(3);

    });

    it('filters the custom filters registration by scope', function (): void {

        $fieldsForBaseScope = [
            TextInput::make('about')
                ->label(__('About')),
        ];

        $fieldsForTemplateScope = [
            TextInput::make('theme_about')
                ->label(__('Theme About')),

            MarkdownEditor::make('about_theme')
                ->label(__('Theme About Markdown')),
        ];

        $this->manager->registerFields(
            resource: PageResource::class,
            tab: ResourceTab::GENERAL->value,
            fields: $fieldsForBaseScope,
        );

        $templateScope = new CustomFieldsScope(
            key: 'template',
            value: 'home.blade.php'
        );

        $this->manager->registerFields(
            resource: PageResource::class,
            tab: ResourceTab::GENERAL->value,
            scope: $templateScope,
            fields: $fieldsForTemplateScope

        );

        $scopedFields = $this->manager->getScopeSchema(
            resource: PageResource::class,
            tab: ResourceTab::GENERAL->value,
            scope: $templateScope
        );

        expect($scopedFields)
            ->toHaveCount(2);

    });

    it('returns a list of fields to be registered on the general scope', function (): void {

        $fields = [
            TextInput::make('about')
                ->label(__('About')),
        ];

        $this->manager->registerFields(
            resource: PageResource::class,
            tab: ResourceTab::class,
            fields: $fields,
        );

        $scopedFields = $this->manager->getScopeSchema(
            resource: PageResource::class,
            tab: ResourceTab::class,
            scope: BaseResourceCustomFieldsScope::make()
        );

        expect($scopedFields)
            ->toHaveCount(1);

    });

    it('registers a custom field', function (): void {
        $this->manager->registerFields(
            resource: PageResource::class,
            tab: ResourceTab::GENERAL->value,
            fields: [
                TextInput::make('about')
                    ->label(__('About')),
            ]
        );

        expect($this->manager->container())
            ->toBeInstanceOf(CustomFieldsContainer::class);

    });

});
