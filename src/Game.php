<?php

declare(strict_types=1);

namespace Tennis;

class Game
{
    const MIN_POINTS_TO_WIN = 4;
    const MIN_POINT_DIFFERENCE = 2;

    protected array $points;

    protected bool $lackService = false;

    protected function __construct(
        protected readonly int $id,
        protected Turn $turn
    ) {
        $this->points = [
            $turn->getService()->getId() => 0,
            $turn->getRest()->getId() => 0,
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

    public function addPointToService(): void
    {
        $this->addPointTo($this->turn->getService());
    }

    public function addPointToRest(): void
    {
        $this->addPointTo($this->turn->getRest());
    }

    protected function addPointTo(Player $player): void
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
        $opponent = $this->turn->getOpponent($player);

        return $this->getPoints($player) >= static::MIN_POINTS_TO_WIN &&
            ($this->getPoints($player) - $this->getPoints($opponent)) >= static::MIN_POINT_DIFFERENCE;
    }

    public function getService(): Player
    {
        return $this->turn->getService();
    }

    public function getRest(): Player
    {
        return $this->turn->getRest();
    }

    public function isGameBall(): bool
    {
        return $this->isGameBallSituation($this->turn->getService(), $this->turn->getRest()) ||
            $this->isGameBallSituation($this->turn->getRest(), $this->turn->getService());
    }

    private function isGameBallSituation(Player $player, Player $opponent): bool
    {
        return $this->getPoints($player) >= self::MIN_POINTS_TO_WIN - 1 &&
            $this->getPoints($opponent) < self::MIN_POINTS_TO_WIN - 1;
    }

    public function isLackService(): bool
    {
        return $this->lackService === true;
    }

    public function getPoints(Player $player): int
    {
        return $this->points[$player->getId()];
    }

    public static function create(int $id, Turn $turn): static
    {
        return new static($id, Turn::create($turn->getService(), $turn->getRest()));
    }
}
