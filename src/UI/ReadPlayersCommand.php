<?php

declare(strict_types=1);

namespace Tennis\UI;

class ReadPlayersCommand extends Command
{
    protected $requireLogin = true;

    public function execute(?string $args = null): void
    {
        if (!$this->console->getGame()->isLoggedIn()) {
            $this->console->println("You must be logged in to view players.");
            return;
        }

        $players = $this->console->getGame()->getPlayers();

        if (empty($players)) {
            $this->console->println("No players available.");
            return;
        }

        foreach ($players as $player) {
            $this->console->println("name:{$player->getName()}; id:" . $player->getId());
        }
    }
}
