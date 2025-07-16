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

    public function getPlayer1(): Player
    {
        return clone $this->player1;
    }

    public function getPlayer2(): Player
    {
        return clone $this->player2;
    }

    public function getService(): Player
    {
        return clone $this->service;
    }

    public function getRest(): Player
    {
        return $this->getPlayer1()->is($this->getService())
            ? $this->getPlayer2()
            : $this->getPlayer1();
    }

    /**
     * @return array<int, Player> 
     */
    public function getPlayers(): array
    {
        return [$this->getPlayer1(), $this->getPlayer2()];
    }

    public function switch(): void
    {
        $this->service = $this->getRest();
    }

    public static function create(Player $player1, Player $player2): self
    {
        return new self($player1, $player2, $player1);
    }

    public static function createRandom(Player $player1, Player $player2): self
    {
        $service = rand(0, 1) === 0 ? $player1 : $player2;
        return new self($player1, $player2, $service);
    }
}
