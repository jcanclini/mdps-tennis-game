<?php

declare(strict_types=1);

namespace Tennis\UI;

class CreatePlayerCommand extends Command
{
    protected $requireLogin = true;

    public function execute(?string $args = null): void
    {
        if ($this->isMatchInProgress()) {
            $this->println("You cannot create a player while a match is in progress.");
            return;
        }

        if (empty($args) || !preg_match('/^name:[^;]+$/', $args)) {
            $this->println("Invalid command format. Use 'name:player_name'.");
            return;
        }

        $name = explode(":", $args);

        $this->game->createPlayer($name[1]);
    }
}
