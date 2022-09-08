<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;

class LoadAvgCommand extends AbstractStatCommand
{
    use InteractsWithCli;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'stat:load {--E|endpoint= : The endpoint to ping.}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Sample the load averages.';

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
    protected $statType = ['cpu_load'];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Don't run when no monitors are configured.
        if ($this->monitors->isEmpty()) {
            $this->verboseInfo('No CPU Load monitors configured...');

            return;
        }

        $this->verboseInfo(sprintf('Monitor Config: %s...', $this->monitorConfig->getConfigPath()));

        $this->monitors->each(function ($monitor) {
            // Take the sample if we haven't done so already.
            if (! $this->sampleTaken) {
                $this->sampleTaken = true;

                return $monitor->stat()->sample();
            }
        })->each(function ($monitor) {
            $this->verboseInfo("Testing {$monitor->key}...");

            $monitor->stat()->test();
        });
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule)
    {
        if (config('app.env') === 'production') {
            $schedule->exec('/root/forge-monitor/monitor stat:load')->everyMinute();
        } else {
            $schedule->command(static::class)->everyMinute();
        }
    }
}
