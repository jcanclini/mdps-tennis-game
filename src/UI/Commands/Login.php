<?php

declare(strict_types=1);

namespace Tennis\UI\Commands;

use Tennis\UI\Command;
use Tennis\UI\Views\Authenticated;

class Login extends Command
{
    protected string $validationPattern = '/^name:([^;]+);password:([^;]+)$/';
    protected string $validationMessage = "Invalid command format. Use 'name:your_name;password:your_password'.";

    public function run(): void
    {
        if ($this->tennisController->login($this->matches[1], $this->matches[2])) {
            $authenticated = new Authenticated($this->viewIO, $this->tennisController);
            $authenticated->render();
        } else {
            $this->viewIO->writeLine("Login failed. Please check your credentials.");
        }
    }
}
