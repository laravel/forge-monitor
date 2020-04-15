<?php

namespace App;

use App\Alert;
use App\Monitors\Monitor;
use Illuminate\Support\Facades\Http;

class Notifier
{
    /**
     * Notify Forge of the monitor state.
     *
     * @param  \App\Monitors\Monitor $monitor
     * @param  \App\Alert $alert
     * @return void
     */
    public static function alert(Monitor $monitor, Alert $alert)
    {
        Http::post(config('monitor.endpoint'), [
            'monitor' => $monitor->key,
            'token' => $monitor->token,
            'state' => $alert->monitor_state
        ]);
    }
}
