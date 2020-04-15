<?php

namespace App\Console\Commands;

use App\Monitors\MonitorConfig;
use Exception;
use Illuminate\Console\Command;

abstract class AbstractStatCommand extends Command
{
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

        if (!$this->statType) {
            throw new Exception('No statType defined.');
        }

        $this->monitors = $monitorConfig->forType($this->statType);
    }
}
