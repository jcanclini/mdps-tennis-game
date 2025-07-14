<?php

declare(strict_types=1);

namespace Tennis;

class Scoreboard
{
    private const PLAYER1 = 0;
    private const PLAYER2 = 1;

    private const SCORE_MAP = [
        0 => '0',
        1 => '15',
        2 => '30',
        3 => '40',
    ];

    private TennisMatch $match;

    public function setMatch(TennisMatch $match): void
    {
        $this->match = $match;
    }

    public function getScore(): array
    {
        if ($this->match->isTieBreak()) {
            return $this->match->getPoints();
        }

        $points = $this->match->getPoints();

        if (
            $points[self::PLAYER1] >= Game::MIN_POINTS_TO_WIN - 1 &&
            $points[self::PLAYER2] >= Game::MIN_POINTS_TO_WIN - 1
        ) {
            $diff = $points[0] - $points[1];

            return match ($diff) {
                0 => ['40', '40'],
                1 => ['Ad', '40'],
                -1 => ['40', 'Ad']
            };
        }

        return [
            self::SCORE_MAP[$points[self::PLAYER1]],
            self::SCORE_MAP[$points[self::PLAYER2]]
        ];
    }
}
