<?php

declare(strict_types=1);

namespace Tennis\UI\Commands;

use Tennis\UI\Command;
use Tennis\UI\Views\Authenticated;

class Login extends Command
{
    public function execute(?string $args = null): void
    {
        if (!preg_match('/name:([^;]+);password:([^;]+)/', $args, $matches)) {
            $this->viewIO->writeLine("Invalid command format. Use 'name:your_name;password:your_password'.");
            return;
        }

        if ($this->tennisController->login($matches[1], $matches[2])) {
            $authenticated = new Authenticated($this->tennisController);
            $authenticated->setIO($this->viewIO);
            $authenticated->render();
        } else {
            $this->viewIO->writeLine("Login failed. Please check your credentials.");
        }
    }
}
