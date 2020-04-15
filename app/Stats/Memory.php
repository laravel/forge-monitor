<?php

namespace App\Stats;

use App\MemoryUsage;

class Memory implements Stat
{
    use ExecuteCommands;

    /**
     * Sample the stat.
     *
     * @return void
     */
    public function sample()
    {
        $memory = $this->getMemoryInfo();

        MemoryUsage::create([
            'total' => $memory['total'],
            'available' => $memory['free'],
            'used' => $memory['used'],
            'free' => $memory['free'],
        ]);
    }

    /**
     * Test the stat.
     *
     * @return bool
     */
    public function test()
    {
        //
    }

    /**
     * Get the memory info.
     *
     * @return array
     */
    protected function getMemoryInfo()
    {
        return once(function () {
            $memory = [];

            if (is_readable('/proc/meminfo')) {
                $total = (int) $this->executeCommand("grep MemTotal /proc/meminfo | awk '{print $2}'");
                $available = (int) $this->executeCommand("grep MemAvailable /proc/meminfo | awk '{print $2}'");
                $used = $total - $available;

                $memory = [
                    'total' => $total,
                    'used' => ($used / $total) * 100,
                    'free' => 100 - (($used / $total) * 100),
                ];
            }

            return $memory;
        });
    }
}
