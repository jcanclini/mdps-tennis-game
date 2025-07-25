<?php

declare(strict_types=1);

namespace Tennis;

class Game
{
    const MIN_POINTS_TO_WIN = 4;
    const MIN_POINT_DIFFERENCE = 2;

    /**
     * @var array<int, int> Points scored by each player.
     */
    protected array $points;

    protected bool $lackService = false;

    public function __construct(
        protected readonly int $id,
        protected readonly Turn $turn
    ) {
        $turn->switch();
        $this->points = [
            $turn->getServiceId() => 0,
            $turn->getRestId() => 0,
        ];
    }

    public function lackService(): void
    {
        if ($this->isLackService()) {
            $this->addPointTo($this->turn->getRest());
        } else {
            $this->lackService = true;
        }
    }

    public function addPointTo(Player $player): void
    {
        assert(!$this->isFinished(), 'Game is already finished.');

        $this->points[$player->getId()]++;
        $this->lackService = false;
    }

    public function isFinished(): bool
    {
        foreach ($this->turn->getPlayers() as $player) {
            if ($this->isWinner($player)) {
                return true;
            }
        }
        return false;
    }

    public function isWinner(Player $player): bool
    {
        return $this->getPoints($player) >= static::MIN_POINTS_TO_WIN &&
            ($this->getPoints($player) - $this->getPoints($this->turn->getOpponent($player))) >= static::MIN_POINT_DIFFERENCE;
    }

    public function isGameBall(): bool
    {
        foreach ($this->turn->getPlayers() as $player) {
            if (
                $this->getPoints($player) >= static::MIN_POINTS_TO_WIN - 1 &&
                $this->getPoints($player) - $this->getPoints($this->turn->getOpponent($player)) >= 1
            ) {
                return true;
            }
        }
        return false;
    }

    private function isLackService(): bool
    {
        return $this->lackService === true;
    }

    private function getPoints(Player $player): int
    {
        return $this->points[$player->getId()];
    }

    public function getScoreboard(): Scoreboard
    {
        return new Scoreboard(
            $this->points,
            $this->isLackService(),
            $this->isGameBall()
        );
    }
}
