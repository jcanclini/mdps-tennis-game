<?php

declare(strict_types=1);

namespace Tennis\UI;

class ReadPlayersCommand extends Command
{
    protected $requireLogin = true;

    public function execute(?string $args = null): void
    {
        if (!$this->game->isLoggedIn()) {
            $this->println("You must be logged in to view players.");
            return;
        }

        $players = $this->game->getPlayers();

        if (empty($players)) {
            $this->println("No players available.");
            return;
        }

        foreach ($players as $player) {
            $this->println("name:{$player->getName()}; id:" . $player->getId());
        }
    }
}
