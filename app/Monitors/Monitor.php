<?php

namespace App\Monitors;

use App\Stats\CpuLoad;
use App\Stats\DiskSpace;
use App\Stats\FreeMemory;
use App\Stats\LoadAvg;
use App\Stats\UsedMemory;

class Monitor
{
    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $operator;

    /**
     * @var float
     */
    public $threshold;

    /**
     * @var int
     */
    public $minutes;

    /**
     * @var string
     */
    public $token;

    /**
     * Create a new monitor instance.
     *
     * @param  string  $key
     * @param  string  $type
     * @param  string  $operator
     * @param  float  $threshold
     * @param  int  $minutes
     * @param  string  $token
     * @return void
     */
    public function __construct($key, $type, $operator, $threshold, $minutes, $token)
    {
        $this->key = $key;
        $this->type = $type;
        $this->operator = $operator;
        $this->threshold = (float) $threshold;
        $this->minutes = (int) $minutes;
        $this->token = $token;

        // Disk Monitors only need 1 check.
        if ($this->type === 'disk') {
            $this->minutes = 1;
        }
    }

    /**
     * Return the stat instance for the given Monitor.
     *
     * @return \App\Stats\Stat
     */
    public function stat()
    {
        switch ($this->type) {
            case 'disk': return new DiskSpace($this);
            case 'cpu_load': return new LoadAvg($this);
            case 'free_memory': return new FreeMemory($this);
            case 'used_memory': return new UsedMemory($this);
        }
    }
}
