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
        return $this->getWinner() !== null;
    }

    public function getWinner(): ?Player
    {
        if ($this->isWinner($this->turn->getService())) {
            return $this->turn->getService();
        }

        if ($this->isWinner($this->turn->getRest())) {
            return $this->turn->getRest();
        }

        return null;
    }

    protected function isWinner(Player $player): bool
    {
        return $this->getPoints($player) >= static::MIN_POINTS_TO_WIN &&
            ($this->getPoints($player) - $this->getPoints($this->turn->getOpponent($player))) >= static::MIN_POINT_DIFFERENCE;
    }

    public function isGameBall(): bool
    {
        return $this->isGameBallSituation($this->turn->getService(), $this->turn->getRest()) ||
            $this->isGameBallSituation($this->turn->getRest(), $this->turn->getService());
    }

    private function isGameBallSituation(Player $player, Player $opponent): bool
    {
        return $this->getPoints($player) >= self::MIN_POINTS_TO_WIN - 1 &&
            $this->getPoints($player) - $this->getPoints($opponent) >= 1;
    }

    public function isLackService(): bool
    {
        return $this->lackService === true;
    }

    public function getPoints(Player $player): int
    {
        return $this->points[$player->getId()];
    }
}
