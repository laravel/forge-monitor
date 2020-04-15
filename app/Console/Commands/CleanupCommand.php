<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-up';

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
}
