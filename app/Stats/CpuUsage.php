<?php

namespace App\Stats;

use App\CpuUsage as CpuUsageModel;
use App\Monitors\Monitor;
use Illuminate\Support\Facades\DB;

class CpuUsage extends AbstractStat implements Stat
{
    /**
     * Create a new Stat instance.
     *
     * @param  \App\Monitors\Monitor $monitor
     * @return void
     */
    public function __construct(Monitor $monitor)
    {
        $this->monitor = $monitor;
    }

    /**
     * Sample the stat.
     *
     * @return void
     */
    public function sample()
    {
        /*
        |--------------------------------------------------------------------------
        | /proc/stat
        |--------------------------------------------------------------------------
        |
        | /proc/stat is actually the Linux kernel's system statistics pool, where
        | the kernel writes immediate statistics about its behavior. The first
        | line of the file has the format
        | cpuN user nice system idle io_wait irq soft_irq steal guest guest_nice
        | awk:  $2   $3    $4    $5    $6     $7    $8      $9
        |
        | Where user, nice, and so forth are expressed in number of jiffies
        | (approx 1/100th of a second) spent running processes in those
        | categories.
        |
        | The first line of /proc/stat is an aggregation of all CPU cores, so we
        | can use the one line as opposed to reading x lines and summing them
        | ourselves.
        |
        | We calculate how much the CPU is idle from the stats
        | thus we can determine how busy the CPU really is.
        |
        | See man 5 proc for more information.
        |
        */

        if (is_readable("/proc/stat")) {
            $cpuIdle = (float) $this->executeCommand("grep 'cpu ' /proc/stat | awk '{idle=($5*100)/($2+$3+$4+$5+$6+$7+$8+$9)} END {print idle}'");
            $cpuUsage = (float) 100 - $cpuIdle;

            CpuUsageModel::create([
                'used' => $cpuUsage,
                'idle' => $cpuIdle,
            ]);
        }
    }

    /**
     * Test the stat.
     *
     * @return bool
     */
    public function test()
    {
        $op = $this->getOperator();

        $results = DB::select("SELECT
    CASE WHEN used {$op} ? THEN 'ALERT' ELSE 'OK' END AS currentState,
    IFNULL(alerts.monitor_state, 'UNKNOWN') AS lastState
FROM (
    SELECT * FROM cpu_usage WHERE created_at >= DATETIME('NOW', ?) ORDER BY created_at DESC LIMIT ?
) _samples
LEFT JOIN (SELECT * FROM alerts WHERE monitor_id = ? AND monitor_type = ? ORDER BY created_at DESC LIMIT 1) alerts", [
            $this->monitor->threshold,
            '-'.($this->monitor->minutes + 1).' minutes',
            $this->monitor->minutes + 1,
            $this->monitor->key,
            $this->monitor->type,
        ]);

        return $this->testResults($results);
    }

}
