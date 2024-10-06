<?php

function wordsphere_path(string $path = ''): string
{
    return __DIR__.'/../'.$path;
}

if (! function_exists('wordsphere_path')) {
    /**
     * Get the path to the base of the wordsphere source code.
     */
    function wordsphere_path(string $path = ''): string
    {
        return __DIR__.'/../'.$path;
    }
}
