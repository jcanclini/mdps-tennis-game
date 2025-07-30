<?php

declare(strict_types=1);

namespace Tennis\UI\Commands;

use Tennis\UI\Command;

class CreatePlayer extends Command
{
    protected string $validationPattern = '/^name:([^;]+)$/';
    protected string $validationMessage = "Invalid command format. Use 'name:player_name'.";

    protected function run(): void {
        $this->tennisController->createPlayer($this->matches[1]);
    }
}