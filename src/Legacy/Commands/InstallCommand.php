<?php

declare(strict_types=1);

namespace WordSphere\Core\Legacy\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    public $signature = 'wordsphere:install';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
