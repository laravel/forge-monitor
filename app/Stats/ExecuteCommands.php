<?php

namespace App\Stats;

use RuntimeException;
use TitasGailius\Terminal\Terminal;

trait ExecuteCommands
{
    /**
     * Execute a command.
     *
     * @param  string $command
     * @return string
     *
     * @throws \RuntimeException
     */
    public function executeCommand($command)
    {
        $response = Terminal::run($command);

        if (!$response->successful()) {
            throw new RuntimeException(
                "Command failed [{$command}]: " . $response->output()
            );
        }

        $output = '';

        foreach ($response->lines() as $line) {
            $output .= $line;
        }

        return $output;
    }
}
