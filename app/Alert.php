<?php

namespace App;

use App\Monitors\Monitor;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Create an alert for a monitor's state.
     *
     * @param  \App\Monitors\Monitor $monitor
     * @param  string $state
     * @return $this
     */
    public static function createForMonitor(Monitor $monitor, $state)
    {
        $alert = static::create([
            'monitor_id' => $monitor->key,
            'monitor_type' => $monitor->type,
            'monitor_state' => $state,
        ]);

        Notifier::alert($monitor, $alert);

        return $alert;
    }
}
