<?php

declare(strict_types=1);

namespace Tennis\UI\Commands;

use Tennis\UI\Command;

class CreateReferee extends Command
{
    public function execute(?string $args = null): void
    {
        if (!preg_match('/^name:([^;]+);password:([^;]+)$/', $args, $matches)) {
            $this->viewIO->writeLine("Invalid command format. Use 'name:your_name;password:your_password'.");
            return;
        }

        $this->tennisController->createReferee($matches[1], $matches[2]);
    }
}
