<?php

namespace App\Stats;

use App\Alert;
use App\Monitors\Monitor;

abstract class AbstractStat
{
    use ExecuteCommands;

    const OK = "OK";
    const ALERT = "ALERT";
    const UNKNOWN = "UNKNOWN";

    protected $totalResults;
    protected $lastState;
    protected $lastAlertState;
    protected $alertStreak = 0;
    protected $prevAlertStreak = 0;
    protected $okStreak = 0;

    /**
     * The monitor instance.
     *
     * @var \App\Monitors\Monitor
     */
    protected $monitor;

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
     * Test whether the results array passed the monitor requirements.
     *
     * @param  \App\Monitors\Monitor $monitor
     * @param  array $results
     * @return bool
     */
    protected function testResults(array $results)
    {
        $this->totalResults = count($results);

        // Not enough data to check.
        if ($this->totalResults < $this->monitor->minutes) {
            return false;
        }

        foreach ($results as $row) {
            if ($row->currentState == self::OK) {
                $this->okStreak++;

                if ($this->lastState == self::ALERT) {
                    $this->prevAlertStreak = $this->alertStreak;
                    $this->alertStreak = 0;
                }
            } elseif ($row->currentState == self::ALERT) {
                $this->alertStreak++;

                if ($this->lastState == self::OK) {
                    $this->okStreak = 0;
                }
            }

            $this->lastState = $row->currentState;
            $this->lastAlertState = $row->lastState;
        }

        $this->handleState();

        return true;
    }

    /**
     * Handle the alert state for the monitor.
     *
     * @return void
     */
    protected function handleState()
    {
        // First time notification.
        if ($this->lastAlertState == self::UNKNOWN) {
            if ($this->lastState == self::OK) {
                Alert::createForMonitor($this->monitor, self::OK);
            } else {
                Alert::createForMonitor($this->monitor, self::ALERT);
            }
            return;
        }

        // Notification updates.
        if ($this->lastState == self::ALERT) {
            if ($this->alertStreak == $this->monitor->minutes) {
                if ($this->lastAlertState != self::ALERT) {
                    Alert::createForMonitor($this->monitor, self::ALERT);
                }
            }
        } elseif ($this->lastState == self::OK) {
            if ($this->lastAlertState !== self::OK) {
                Alert::createForMonitor($this->monitor, self::OK);
            }
        } else {
            // Throw exception? Unknown state.
        }
    }

    /**
     * Get the real query operator.
     *
     * @return string
     */
    protected function getOperator()
    {
        return $this->monitor->operator == "gte" ? ">=" : "<=";
    }
}
