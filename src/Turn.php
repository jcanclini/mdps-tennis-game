<?php

declare(strict_types=1);

namespace Tennis;

class Turn
{
    public function __construct(
        private readonly Player $player1,
        private readonly Player $player2,
        private Player $service,
    ) {}

    public function isService(Player $player): bool
    {
        return $this->service === $player;
    }

    public function getOpponent(Player $player): Player
    {
        return $this->isService($player) ? $this->getRest() : $this->getService();
    }

    public function switch(): void
    {
        $this->service = $this->getRest();
    }

    public function getService(): Player
    {
        return $this->service;
    }

    public function getRest(): Player
    {
        return $this->player1 === $this->service ? $this->player2 : $this->player1;
    }

    public static function create(Player $player1, Player $player2): self
    {
        return new self($player1, $player2, $player1);
    }
}
