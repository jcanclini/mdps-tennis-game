<?php

declare(strict_types=1);

namespace Tennis;

class Turn
{
    private bool $isFirtstTurn = true;

    /**
     * @param array{0: Player, 1: Player} $players 
     * @return void 
     */
    public function __construct(
        private array $players
    ) {}

    public function getOpponent(Player $player): Player
    {
        return $player->is($this->players[0]) ? $this->players[1] : $this->players[0];
    }

    public function getService(): Player
    {
        return $this->players[0];
    }

    public function getServiceId(): int
    {
        return $this->players[0]->getId();
    }

    public function getRest(): Player
    {
        return $this->players[1];
    }

    public function getRestId(): int
    {
        return $this->players[1]->getId();
    }

    /**
     * @return array<int, Player> 
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    public function switch(): void
    {
        if ($this->isFirtstTurn) {
            $this->isFirtstTurn = false;
            return;
        }

        $this->players = array_reverse($this->players);
    }

    /**
     * Creates a Turn instance with the given players.
     * @param array<int, Player> $players 
     * @return Turn 
     */
    public static function create(array $players): self
    {
        return new self($players, $players[0]);
    }

    public static function createRandom(array $players): self
    {
        $service = $players[array_rand($players)];
        return new self($players, $service);
    }
}
