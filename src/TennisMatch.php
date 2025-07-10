<?php

declare(strict_types=1);

namespace Tennis;

use DateTimeImmutable;

class TennisMatch
{
    private ?Player $winner = null;

    private array $sets = [];

    private Turn $turn;

    private array $points;

    private \DateTimeImmutable $date;

    private function __construct(
        private readonly int $id,
        private Player $player1,
        private Player $player2,
        private readonly int $setsToPlay
    ) {
        assert($setsToPlay === 3 || $setsToPlay === 5, 'Max sets must be either 3 or 5.');

        $this->date = new \DateTimeImmutable();

        $this->turn = Turn::create($player1, $player2);

        $this->points = [
            $player1->getId() => 0,
            $player2->getId() => 0,
        ];

        $this->sets[] = $this->createSet();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function addPointToService(): void
    {
        assert(!$this->winner, 'Match is already finished.');
        $this->currentSet()->addPointToService();
        $this->checkStatus();
    }

    public function addPointToRest(): void
    {
        assert(!$this->winner, 'Match is already finished.');
        $this->currentSet()->addPointToRest();
        $this->checkStatus();
    }

    private function checkStatus(): void
    {
        if (!$this->currentSet()->isFinished()) {
            return;
        }

        $this->currentSet()->getWinner() === $this->player1
            ? $this->points[$this->player1->getId()]++
            : $this->points[$this->player2->getId()]++;

        if ($this->points[$this->player1->getId()] >= $this->getMinSetsToWin()) {
            $this->winner = $this->player1;
            return;
        }
        if ($this->points[$this->player2->getId()] >= $this->getMinSetsToWin()) {
            $this->winner = $this->player2;
            return;
        }

        $this->turn->switch();

        $this->sets[] = $this->createSet();;
    }

    /**
     * @return Set[] 
     */
    public function getSets(): array
    {
        return $this->sets;
    }

    public function getPendingSets(): int
    {
        return $this->setsToPlay - count($this->sets);
    }

    public function getPlayerPoints(Player $player): int
    {
        return $this->currentSet()->getCurrentGame()->getPoints($player);
    }

    public function hasLackService(): bool
    {
        return $this->currentSet()->getCurrentGame()->isLackService();
    }

    public function lackService(): void
    {
        $this->currentSet()->getCurrentGame()->lackService();
    }

    public function getCurrentGameService(): Player
    {
        return $this->currentSet()->getCurrentGame()->getService();
    }

    public function isGameBall(): bool
    {
        return $this->currentSet()->getCurrentGame()->isGameBall();
    }

    public function isSetBall(): bool
    {
        return $this->currentSet()->isSetBall();
    }

    public function isTieBreak(): bool
    {
        return $this->currentSet()->isTieBreak();
    }

    public function getWinner(): ?Player
    {
        return $this->winner;
    }

    public function isFinished(): bool
    {
        return $this->winner !== null;
    }

    /**
     * @return array<int, Player> 
     */
    public function getPlayers(): array
    {
        return [$this->player1, $this->player2];
    }

    public function getMinSetsToWin(): int
    {
        return $this->setsToPlay === 3 ? 2 : 3;
    }

    private function createSet(): Set
    {
        return Set::create(
            count($this->sets) + 1,
            $this->player1,
            $this->player2
        );
    }

    private function currentSet(): Set
    {
        return end($this->sets) ?: $this->createSet();
    }

    public static function create(
        int $id,
        Player $player1,
        Player $player2,
        int $setToPlay
    ): self {
        return new self($id, $player1, $player2, $setToPlay);
    }
}
