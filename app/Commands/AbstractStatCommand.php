<?php

namespace App\Commands;

use App\Monitors\MonitorConfig;
use Exception;
use LaravelZero\Framework\Commands\Command;

abstract class AbstractStatCommand extends Command
{
    /**
     * The monitor config instance.
     *
     * @var \App\Monitors\MonitorConfig
     */
    protected $monitorConfig;

    /**
     * The configured monitors for the stat.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $monitors;

    /**
     * The stat type to look for when running the command.
     *
     * @var array|string
     */
    protected $statType;

    /**
     * Create a new Disk Stat Command instance.
     *
     * @param  \App\Monitors\MonitorConfig  $monitorConfig
     * @return void
     */
    public function __construct(MonitorConfig $monitorConfig)
    {
        parent::__construct();

        if (! $this->statType) {
            throw new Exception('No statType defined.');
        }

        $this->monitorConfig = $monitorConfig;
        $this->monitors = $monitorConfig->forType($this->statType);
    }
}
