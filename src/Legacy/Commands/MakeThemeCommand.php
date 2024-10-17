<?php

declare(strict_types=1);

namespace WordSphere\Core\Legacy\Commands;

use Illuminate\Console\Command;
use WordSphere\Core\Legacy\Support\Themes\ThemeManager;

class MakeThemeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wordsphere:make-theme {vendor} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It created a new theme';

    public function __construct(
        private readonly ThemeManager $themeManager,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (! $this->themeManager->directoryExists(config('wordsphere.themes.path'))) {
            $this->error('Theme directory does not exist');
            //Let's Create the themes folder
        }
    }
}
