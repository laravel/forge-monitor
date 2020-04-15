<?php

namespace App\Stats;

use App\LoadAvg as LoadAvgModel;
use App\Monitors\Monitor;
use Illuminate\Support\Facades\DB;

class LoadAvg extends AbstractStat implements Stat
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
        if (is_readable("/proc/cpuinfo")) {
            $cores = (int) $this->executeCommand('cat /proc/cpuinfo | grep "^processor" | wc -l');

            // https://stackoverflow.com/a/38085813
            $loads = sys_getloadavg();
            $loadPercent = round($loads[0] / max(1, $cores) * 100, 2);

            LoadAvgModel::create([
                'load_avg' => $loads[0],
                'load_avg_percent' => $loadPercent,
                'cpus' => (int) $cores,
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
    CASE WHEN load_avg_percent {$op} ? THEN 'ALERT' ELSE 'OK' END AS currentState,
    IFNULL(alerts.monitor_state, 'UNKNOWN') AS lastState
FROM (
    SELECT * FROM load_avgs WHERE created_at >= DATETIME('NOW', ?) ORDER BY created_at DESC LIMIT ?
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
