<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class CleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stat:clean-up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old stat data.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::delete('DELETE FROM load_avgs WHERE created_at <= DATETIME("NOW", "-INTERVAL 1 WEEK")');
        DB::delete('DELETE FROM disk_usages WHERE created_at <= DATETIME("NOW", "-INTERVAL 1 WEEK")');
        DB::delete('DELETE FROM memory_usages WHERE created_at <= DATETIME("NOW", "-INTERVAL 1 WEEK")');
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
            $schedule->exec('/root/forge-monitor/monitor stat:clean-up')->everyMinute();
        } else {
            $schedule->command(static::class)->everyMinute();
        }
    }
}
