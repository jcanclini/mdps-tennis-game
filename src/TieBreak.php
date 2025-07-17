<?php

declare(strict_types=1);

namespace Tennis;

class TieBreak extends Game
{
    public const MIN_POINTS_TO_WIN = 6;

    public function addPointTo(Player $player): void
    {
        parent::addPointTo($player);
        $this->switchTurn();
    }

    private function switchTurn(): void
    {
        if (array_sum($this->points) === 1 || array_sum($this->points) % 2 === 1) {
            $this->turn->switch();
        }
    }
}
