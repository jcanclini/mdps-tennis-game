<?php

declare(strict_types=1);

namespace Tennis\UI\Commands;

use Tennis\TennisMatch;
use Tennis\UI\Command;
use Tennis\UI\Views\TennisMatch as ViewsTennisMatch;

class CreateMatch extends Command
{
    public function execute(?string $args = null): void
    {
        if (empty($args) || !preg_match('/^sets:(\d+);ids:(\d+(?:,\d+)*)$/', $args, $matches)) {
            $this->viewIO->writeLine("Invalid command format. Use 'sets:number_of_sets;ids:player_id1,player_id2'.");
            return;
        }

        if (!in_array((int)$matches[1], TennisMatch::ALLOWED_SETS, true)) {
            $this->viewIO->writeLine("Invalid number of sets. Only 3 or 5 sets are allowed.");
            return;
        }

        $players = [];
        $ids = array_map('intval', explode(',', $matches[2]));
        for ($i = 0; $i < 2; $i++) {
            $players[] = $this->tennisController->getPlayer((int) $ids[$i]);
            if (empty($players[$i])) {
                $this->viewIO->writeLine("Player with ID {$ids[$i]} does not exist.");
                return;
            }
        }

        $this->tennisController->createMatch($players, (int)$matches[1]);
        $viewsTennisMatch = new ViewsTennisMatch($this->tennisController, $players);
        $viewsTennisMatch->setIO($this->viewIO);
        $viewsTennisMatch->render();
    }
}
