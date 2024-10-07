<?php

declare(strict_types=1);

namespace WordSphere\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \WordSphere\Core\Wordsphere
 */
class WordSphere extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \WordSphere\Core\WordSphere::class;
    }
}
