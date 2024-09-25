<?php

declare(strict_types=1);

namespace WordSphere\Core\Support\Theme;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

class ThemeManager
{
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
        $finder = new Finder();
        $finder->directories()
            ->in(config('wordsphere.themes.path'))
            ->depth(1);

        $folders = [];
        foreach ($finder as $directory) {
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
