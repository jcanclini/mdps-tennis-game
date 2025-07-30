<?php

declare(strict_types=1);

namespace Tennis\UI\Commands;

use Tennis\UI\Command;

class CreateReferee extends Command
{
    protected string $validationPattern = '/^name:([^;]+);password:([^;]+)$/';
    protected string $validationMessage = "Invalid command format. Use 'name:your_name;;password:your_password'.";

    public function run(): void
    {
        $this->tennisController->createReferee($this->matches[1], $this->matches[2]);
    }
}
