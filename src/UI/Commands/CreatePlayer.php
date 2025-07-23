<?php

declare(strict_types=1);

namespace Tennis\UI\Commands;

use Tennis\UI\Command;

class CreatePlayer extends Command
{
    public function execute(?string $args = null): void
    {
        if (empty($args) || !preg_match('/^name:([^;]+)$/', $args, $matches)) {
            $this->viewIO->writeLine("Invalid command format. Use 'name:player_name'.");
            return;
        }

        $playerName = $matches[1];
        $this->tennisController->createPlayer($playerName);
    }
}
