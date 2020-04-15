<?php

namespace App\Console\Commands;

class DiskStatCommand extends AbstractStatCommand
{
    use InteractsWithCli;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'stat:disk {--E|endpoint= : The endpoint to ping.}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Sample the disk space.';

    /**
     * Whether the sample has been taken.
     *
     * @var bool
     */
    protected $sampleTaken;

    /**
     * The stat type to look for when running the command.
     *
     * @var string
     */
    protected $statType = 'disk';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Don't run when no monitors are configured.
        if ($this->monitors->isEmpty()) {
            $this->verboseInfo("No disk monitors configured...");

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
