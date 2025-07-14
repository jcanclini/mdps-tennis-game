<?php

declare(strict_types=1);

namespace Tennis\UI;

use Tennis\TennisMatch;

class CreateMatchCommand extends Command
{
    protected $requireLogin = true;

    public function execute(?string $args = null): void
    {
        if ($this->isMatchInProgress()) {
            $this->console->println("You cannot create a player while a match is in progress.");
            return;
        }

        if (empty($args) || !preg_match('/^sets:\d+;ids:(\d+(,\d+)*)$/', $args)) {
            $this->console->println("Invalid command format. Use 'sets:number_of_sets;ids:player_id1,player_id2'.");
            return;
        }

        [$sets, $ids] = $this->parseArguments($args);

        if (!in_array((int)$sets, TennisMatch::ALLOWED_SETS, true)) {
            $this->console->println("Invalid number of sets. Only 3 or 5 sets are allowed.");
            return;
        }

        $player1 = $this->console->getGame()->getPlayer((int) $ids[0]);
        $player2 = $this->console->getGame()->getPlayer((int) $ids[1]);

        if (empty($player1) || empty($player2)) {
            $this->console->println("Invalid player IDs provided.");
            return;
        }

        $this->console->getGame()->createMatch($player1, $player2, (int)$sets);
        $this->console->draw();;
    }

    private function parseArguments(string $args): array
    {
        $parts = explode(';', $args);
        $sets = explode(':', $parts[0])[1];
        $ids = explode(':', $parts[1])[1];

        return [$sets, explode(',', $ids[1])];
    }
}
