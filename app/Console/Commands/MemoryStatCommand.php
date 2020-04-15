<?php

namespace App\Console\Commands;

use App\Stats\Memory;

class MemoryStatCommand extends AbstractStatCommand
{
    use InteractsWithCli;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'stat:mem {--E|endpoint= : The endpoint to ping.}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Sample the memory.';

    /**
     * The stat type to look for when running the command.
     *
     * @var array|string
     */
    protected $statType = ['free_memory', 'used_memory'];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->monitors->isEmpty()) {
            $this->verboseInfo("No memory monitors configured...");

            return;
        }

        // Sample the memory stat.
        app(Memory::class)->sample();

        // Filter monitors where they failed the test.
        $this->monitors->each(function ($monitor) {
            $this->verboseInfo("Testing {$monitor->key}...");

            return $monitor->stat()->test();
        });
    }
}
