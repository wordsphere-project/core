<?php

declare(strict_types=1);

use WordSphere\Core\Enums\ContentStatus;
use WordSphere\Core\Filament\Resources\PageResource\Pages\CreatePage;
use WordSphere\Core\Models\Page;

use function WordSphere\Tests\livewire;

describe('page resource supports custom fields', function () {

    it('shows the about us field set that is declared on page custom fields', function () {

        Page::factory()
            ->create(
                attributes: [
                    'path' => '/',
                    'title' => 'Home Page',
                    'template' => 'home.blade.php',
                    'status' => ContentStatus::PUBLISHED->value,
                ]
            );

        livewire(component: CreatePage::class)
            ->assertFormFieldExists('data.about.title');

    });

});
