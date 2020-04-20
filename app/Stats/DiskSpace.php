<?php

namespace App\Stats;

use App\DiskUsage;
use App\Monitors\Monitor;
use Illuminate\Support\Facades\DB;

class DiskSpace extends AbstractStat implements Stat
{
    /**
     * Sample the stat.
     *
     * @return void
     */
    public function sample()
    {
        $totalSpace = disk_total_space('/');
        $freeSpace = disk_free_space('/');
        $usedSpace = $totalSpace - $freeSpace;

        $usedPercent = ($usedSpace / $totalSpace) * 100;
        $freePercent = ($freeSpace / $totalSpace) * 100;

        DiskUsage::create([
            'total' => $totalSpace,
            'free' => $freePercent,
            'used' => $usedPercent,
        ]);
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
    SELECT * FROM disk_usages ORDER BY created_at DESC LIMIT 1
) _samples
LEFT JOIN (SELECT * FROM alerts WHERE monitor_id = ? AND monitor_type = ? ORDER BY created_at DESC LIMIT 1) alerts", [
            $this->monitor->threshold,
            $this->monitor->key,
            $this->monitor->type,
        ]);

        return $this->testResults($results);
    }
}
