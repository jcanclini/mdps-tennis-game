<?php

declare(strict_types=1);

namespace Tennis\UI;

class CreatePlayerCommand extends Command
{
    protected $requireLogin = true;

    public function execute(?string $args = null): void
    {
        if ($this->isMatchInProgress()) {
            $this->console->println("You cannot create a player while a match is in progress.");
            return;
        }

        if (empty($args) || !preg_match('/^name:[^;]+$/', $args)) {
            $this->console->println("Invalid command format. Use 'name:player_name'.");
            return;
        }

        $name = explode(":", $args);

        $this->console->getGame()->createPlayer($name[1]);
    }
}
