<?php

namespace App\Console;

use App\Console\Commands\ParseRecipesCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        ParseRecipesCommand::class,
    ];

    protected function schedule(Schedule $schedule)
    {
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }
}
