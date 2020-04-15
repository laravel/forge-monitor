<?php

namespace App\Stats;

interface Stat
{
    /**
     * Sample the stat.
     *
     * @return void
     */
    public function sample();

    /**
     * Test the stat.
     *
     * @return bool
     */
    public function test();
}
