<?php

namespace App;

use App\Alert;
use App\Monitors\Monitor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

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
        try {
            $response = Http::post(config('monitor.endpoint'), [
                'monitor' => $monitor->key,
                'token' => $monitor->token,
                'state' => $alert->monitor_state
            ]);

            if (!$response->successful()) {
                Log::warning('Forge notification failed', [
                    'monitor' => $monitor->key,
                    'state' => $alert->monitor_state,
                    'status' => $response->status(),
                ]);
            }
        } catch (Throwable $e) {
            Log::warning('Forge notification request failed', [
                'monitor' => $monitor->key,
                'state' => $alert->monitor_state,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
