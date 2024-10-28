<?php

declare(strict_types=1);

namespace WordSphere\Core\Interfaces\Console\Commands\Types;

use Illuminate\Console\Command;

class RegisterTypes extends Command
{
    protected $signature = 'types:register';

    protected $description = 'Register all configured content types';

    public function handle(): void
    {
        $registrars = config('types.registrars', []);

        $this->info('Starting type registration...');

        foreach ($registrars as $registrarClass) {
            if (class_exists($registrarClass)) {
                $this->info("Registering types from: {$registrarClass}");
                $registrar = app()->make($registrarClass);
                $registrar->register();
            }
        }

        $this->info('Type registration completed.');
    }
}
