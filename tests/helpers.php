<?php

declare(strict_types=1);

namespace WordSphere\Tests;

use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

if (!function_exists('\WordSphere\Tests\livewire')) {
    function livewire(string $component, array $props = []): Testable
    {
        return Livewire::test($component, $props);
    }
}
