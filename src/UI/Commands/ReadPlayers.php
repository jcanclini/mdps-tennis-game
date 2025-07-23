<?php

declare(strict_types=1);

namespace Tennis\UI\Commands;

use Tennis\UI\Command;

class ReadPlayers extends Command
{
    public function execute(?string $args = null): void
    {
        $players = $this->tennisController->getPlayers();

        if (empty($players)) {
            $this->viewIO->writeLine("No players available.");
            return;
        }

        foreach ($players as $player) {
            $this->viewIO->writeLine("name:{$player->getName()}; id:" . $player->getId());
        }
    }
}
