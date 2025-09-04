<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CpuStatCommand extends AbstractStatCommand
{
    use InteractsWithCli;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stat:cpu {--E|endpoint= : The endpoint to ping.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sample CPU usage.';

    /**
     * Whether the sample has been taken.
     *
     * @var bool
     */
    protected $sampleTaken;

    /**
     * The stat type to look for when running the command.
     *
     * @var array|string
     */
    protected $statType = ['cpu'];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Don't run when no monitors are configured.
        if ($this->monitors->isEmpty()) {
            $this->verboseInfo("No CPU Load monitors configured...");

            return;
        }

        $this->monitors->each(function ($monitor) {
            if (!$this->sampleTaken) {
                $monitor->stat()->sample();

                $this->sampleTaken = true;
            }
        })->each(function ($monitor) {
            $this->verboseInfo("Testing {$monitor->key}...");

            $monitor->stat()->test();
        });
    }
}
