<?php

declare(strict_types=1);

namespace WordSphere\Core\Legacy\Support\Themes;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;
use WordSphere\Core\Legacy\Settings\AppSettings;

use function explode;
use function Orchestra\Testbench\workbench_path;
use function themes_path;

class ThemeManager
{
    public function __construct(
        private AppSettings $appSettings,
        private Finder $finder,
    ) {}

    public function getCurrentThemeTemplates(): array
    {

        $this->finder
            ->files()
            ->in($this->getCurrentThemeBasePath().'/views/livewire/templates')
            ->name('*.blade.php')
            ->depth(0);

        $files = [];
        foreach ($this->finder as $file) {
            $fileName = $file->getRelativePathname();
            $files[explode('.', $fileName)[0]] = $fileName;
        }

        return $files;
    }

    public function getCurrentThemeBasePath(): string
    {
        $themesPath = themes_path($this->appSettings->theme);

        //Just For Testing Propose
        if (! File::isDirectory($themesPath)) {
            $themesPath = workbench_path('themes/'.$this->appSettings->theme);
        }

        return $themesPath;
    }

    public function getThemes(): array
    {
        $pathsToScan = $this->getThemesFolders();
        $theme = [];
        foreach ($pathsToScan as $path) {
            $themeComposerContent = File::get(
                $path.'/composer.json'
            );

            $theme[] = json_decode($themeComposerContent, true);
        }

        return $theme;

    }

    public function isValid(string $path): bool
    {
        return file_exists($path.'/composer.json');
    }

    public function getThemesFolders(): array
    {
        $this->finder->directories()
            ->in(config('wordsphere.themes.path'))
            ->depth(1);

        $folders = [];
        foreach ($this->finder as $directory) {
            if (! $this->isValid($directory->getPathname())) {
                continue;
            }
            $folders[] = $directory->getPathname();
        }

        return $folders;

    }

    public function directoryExists(string $path): bool
    {
        return File::isDirectory($path);
    }
}
