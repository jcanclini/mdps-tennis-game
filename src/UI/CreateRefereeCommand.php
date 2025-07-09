<?php

declare(strict_types=1);

namespace Tennis\UI;

class CreateRefereeCommand extends Command
{
    public function execute(?string $args = null): void {
    {
        if (!preg_match('/^name:[^;]+;password:[^;]+$/', $args)) {
            $this->println("Invalid command format. Use 'name:your_name;password:your_password'.");
            return;
        }

        [$name, $password] = explode(";", $args);

        $name = explode(":", $name);
        $password = explode(":", $password);

        $this->game->createReferee($name[1], $password[1]);
     }
    }
}
