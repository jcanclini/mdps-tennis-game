<?php

declare(strict_types=1);

namespace Tennis;

class TieBreak extends Game
{
    public const MIN_POINTS_TO_WIN = 6;

    public function __construct(
        int $id,
        Turn $turn
    ) {
        parent::__construct(
            $id,
            Turn::create($turn->getService(), $turn->getRest())
        );
    }

    public function addPointToService(): void
    {
        parent::addPointToService();
        $this->switchTurn();
    }

    public function addPointToRest(): void
    {
        parent::addPointToRest();
        $this->switchTurn();
    }

    private function switchTurn(): void
    {
        if (array_sum($this->points) === 1 || array_sum($this->points) % 2 === 1) {
            $this->turn->switch();
        }
    }
}
