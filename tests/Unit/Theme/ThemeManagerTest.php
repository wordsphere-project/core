<?php

declare(strict_types=1);

namespace WordSphere\Tests\Unit\ThemeManager;

use WordSphere\Core\Settings\AppSettings;
use WordSphere\Tests\TestCase;
use WordSphere\Core\Support\Theme\ThemeManager;


beforeEach(function (): void {
    /* @var TestCase $this */

    AppSettings::fake(
        values: [
            'theme' => 'wordsphere/orbit-theme'
        ],
        loadMissingValues: true
    );

    $this->themeManager = app()->make(
        abstract: ThemeManager::class
    );



});

describe('theme manager', function () {

    it('returns a list of templates', function () {
        expect($this->themeManager->getCurrentThemeTemplates())
            ->toBeArray()
            ->toBe([
                'home' => 'home.blade.php',
                'contact' => 'contact.blade.php',
            ]);
    });

    it('returns a list of themes', function () {
        /* @var TestCase $this */


        $themes = $this->themeManager->getThemes();
        expect($themes)->toBeArray();
    });

    it('return a list of themes folders', function () {
        $themes = $this->themeManager->getThemesFolders();
        expect($themes)->toBeArray()
            ->toContain(base_path('themes/wordsphere/orbit-theme'));

    });

    it('can check if themes folder exists', function () {
        $exists = $this->themeManager
            ->directoryExists(
                config('wordsphere.themes.path')
            );
        expect($exists)->toBeTrue();
    });
});

test('ThemeManager Exists', function () {

    expect($this->themeManager)->toBeInstanceOf(ThemeManager::class);

});
