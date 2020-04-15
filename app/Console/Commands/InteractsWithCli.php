<?php

namespace App\Console\Commands;

trait InteractsWithCli
{
    /**
     * Write a string as information output if verbose enough.
     *
     * @param  string $line
     * @return void
     */
    protected function verboseInfo($line)
    {
        if ($this->getOutput()->isVerbose()) {
            $this->info($line);
        }
    }
}
