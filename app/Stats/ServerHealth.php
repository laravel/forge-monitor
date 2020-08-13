<?php

namespace App\Stats;

use App\Monitors\Monitor;
use App\ServerHealth as BaseServerHealth;
use Illuminate\Support\Facades\DB;

class ServerHealth extends AbstractStat implements Stat
{
    /**
     * Sample the stat.
     *
     * @return void
     */
    public function sample()
    {
        BaseServerHealth::create([
            'requires_restart' => file_exists('/var/run/reboot-required'),
        ]);
    }

    /**
     * Test the stat.
     *
     * @return bool
     */
    public function test()
    {
        $results = DB::select("SELECT
    CASE WHEN requires_restart = 1 THEN 'ALERT' ELSE 'OK' END AS currentState,
    IFNULL(alerts.monitor_state, 'UNKNOWN') AS lastState
FROM (
    SELECT * FROM server_health ORDER BY created_at DESC LIMIT 1
) _samples
LEFT JOIN (SELECT * FROM alerts WHERE monitor_id = ? AND monitor_type = ? ORDER BY created_at DESC LIMIT 1) alerts", [
            $this->monitor->key,
            $this->monitor->type,
        ]);

        return $this->testResults($results);
    }
}
