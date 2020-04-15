<?php

namespace App\Stats;

use TitasGailius\Terminal\Terminal;

trait ExecuteCommands
{
    /**
     * Execute a command.
     *
     * @param  string $command
     * @return string
     */
    public function executeCommand($command)
    {
        $output = '';

        $response = Terminal::run($command);

        foreach ($response->lines() as $line) {
            $output .= $line;
        }

        return $output;
    }
}
