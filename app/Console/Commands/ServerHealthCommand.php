<?php

namespace App\Console\Commands;

class ServerHealthCommand extends AbstractStatCommand
{
    use InteractsWithCli;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stat:health {--E|endpoint= : The endpoint to ping.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detect the general healthiness of a server.';

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
    protected $statType = 'health';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->monitors->isEmpty()) {
            $this->verboseInfo("No health monitors configured...");

            return;
        }

        $this->monitors->each(function ($monitor) {
            // Take the sample if we haven't done so already.
            if (!$this->sampleTaken) {
                $this->sampleTaken = true;

                return $monitor->stat()->sample();
            }
        })->each(function ($monitor) {
            $this->verboseInfo("Testing {$monitor->key}...");

            $monitor->stat()->test();
        });
    }
}
