<?php

declare(strict_types=1);

use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentPage;
use WordSphere\Core\Interfaces\Filament\Resources\PageResource\Pages\CreatePage;
use WordSphere\Core\Legacy\Enums\ContentStatus;

use function WordSphere\Tests\livewire;

describe('page resource supports custom fields', function (): void {

    it('shows the about us field set that is declared on page custom fields', function (): void {

        EloquentPage::factory()
            ->create(
                attributes: [
                    'path' => '/',
                    'title' => 'Home Page',
                    'template' => 'home.blade.php',
                    'status' => ContentStatus::PUBLISHED->value,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]
            );

        livewire(component: CreatePage::class)
            ->assertFormFieldExists('custom_fields.about.title');

    });

});
