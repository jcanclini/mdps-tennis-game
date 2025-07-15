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

    protected function __construct(
        protected readonly int $id,
        protected Player $service,
        protected Player $rest
    ) {
        $this->points = [
            $service->getId() => 0,
            $rest->getId() => 0,
        ];
    }

    public function lackService(): void
    {
        if ($this->isLackService()) {
            $this->addPointTo($this->rest);
        } else {
            $this->lackService = true;
        }
    }

    public function addPointToService(): void
    {
        $this->addPointTo($this->service);
    }

    public function addPointToRest(): void
    {
        $this->addPointTo($this->rest);
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
        if ($this->isWinner($this->service)) {
            return $this->service;
        }

        if ($this->isWinner($this->rest)) {
            return $this->rest;
        }

        return null;
    }

    protected function isWinner(Player $player): bool
    {
        $opponent = $player === $this->service ? $this->rest : $this->service;

        return $this->getPoints($player) >= static::MIN_POINTS_TO_WIN &&
            ($this->getPoints($player) - $this->getPoints($opponent)) >= static::MIN_POINT_DIFFERENCE;
    }

    public function getService(): Player
    {
        return $this->service;
    }

    public function getRest(): Player
    {
        return $this->rest;
    }

    public function isGameBall(): bool
    {
        return $this->isGameBallSituation($this->service, $this->rest) ||
            $this->isGameBallSituation($this->rest, $this->service);
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

    public static function create(int $id, Turn $turn): self
    {
        return new self(
            $id,
            $turn->getService(),
            $turn->getRest()
        );
    }
}
