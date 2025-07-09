<?php

declare(strict_types=1);

namespace Tennis\UI;

class CreatePlayerCommand extends Command
{
    public function execute(?string $args = null): void
    {
        if (!preg_match('/^name:[^;]+$/', $args)) {
            $this->println("Invalid command format. Use 'name:player_name'.");
            return;
        }

        $name = explode(":", $args);

        $this->game->createPlayer($name[1]);
    }
}