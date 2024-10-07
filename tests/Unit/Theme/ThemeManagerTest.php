<?php

declare(strict_types=1);

namespace Tests\Unit\ThemeManager;

use Tests\TestCase;
use WordSphere\Core\Support\Theme\ThemeManager;


beforeEach(function (): void {
    /* @var TestCase $this */

    $this->themeManager = new ThemeManager;
});

describe('theme manager', function () {


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
