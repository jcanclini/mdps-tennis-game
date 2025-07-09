<?php

declare(strict_types=1);

namespace Tennis\UI;

class CreateMatchCommand extends Command
{
    public function execute(?string $args = null): void
    {
        if (!preg_match('/^sets:\d+;ids:(\d+(,\d+)*)$/', $args)) {
            $this->println("Invalid command format. Use 'sets:number_of_sets;ids:player_id1,player_id2'.");
            return;
        }

        [$sets, $ids] = explode(";", $args);

        $sets = explode(":", $sets);
        $ids = explode(":", $ids);
        [$id1, $id2] = explode(",", $ids[1]);

        if ($sets[1] != 3 && $sets[1] != 5) {
            $this->println("Invalid number of sets. Only 3 or 5 sets are allowed.");
            return;
        }

        $player1 = $this->game->getPlayer((int) $id1);
        $player2 = $this->game->getPlayer((int) $id2);

        if (empty($player1) || empty($player2)) {
            $this->println("Invalid player IDs provided.");
            return;
        }

        $this->game->createMatch($player1, $player2, (int)$sets);
        $this->game->drawScoreboard();
    }
}
