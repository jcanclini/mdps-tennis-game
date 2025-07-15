<?php

declare(strict_types=1);

namespace Tennis;

use DateTimeImmutable;

class TennisMatch
{
    const ALLOWED_SETS = [3, 5];

    const MIN_SETS_TO_WIN = [3 => 2, 5 => 3];

    private array $sets = [];

    private Turn $turn;

    private \DateTimeImmutable $date;

    private function __construct(
        private readonly int $id,
        private Player $player1,
        private Player $player2,
        private readonly int $setsToPlay
    ) {
        assert(in_array($setsToPlay, self::ALLOWED_SETS), 'Max sets must be either 3 or 5.');
        $this->turn = Turn::create($player1, $player2);
        $this->sets[] = $this->createSet();
        $this->date = new \DateTimeImmutable();
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
        assert($this->getWinner() === null, 'Match is already finished.');
        $this->currentSet()->addPointToService();
        if ($this->currentSet()->isFinished()) {
            $this->sets[] = $this->createSet();
        }
    }

    public function addPointToRest(): void
    {
        assert($this->getWinner() === null, 'Match is already finished.');
        $this->currentSet()->addPointToRest();
        if ($this->currentSet()->isFinished()) {
            $this->sets[] = $this->createSet();;
        }
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

    public function getPoints(): array
    {
        return [
            $this->currentSet()->getCurrentGame()->getPoints($this->player1),
            $this->currentSet()->getCurrentGame()->getPoints($this->player2),
        ];
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

    public function isMatchBall(): bool
    {
        return $this->getPendingSets() === 1 && $this->currentSet()->isSetBall();
    }

    public function isTieBreak(): bool
    {
        return $this->currentSet()->isTieBreak();
    }

    public function isFinished(): bool
    {
        return $this->getWinner() !== null;
    }

    public function getWinner(): ?Player
    {
        if ($this->isWinner($this->player1)) {
            return $this->player1;
        }

        if ($this->isWinner($this->player2)) {
            return $this->player2;
        }

        return null;
    }

    private function isWinner(Player $player): bool
    {
        $setsWon = 0;
        foreach ($this->sets as $set) {
            if ($set->isFinished() && $set->getWinner() === $player) {
                $setsWon++;
            }
        }

        return $setsWon >= $this->getMinSetsToWin();
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
        return self::MIN_SETS_TO_WIN[$this->setsToPlay];
    }

    private function createSet(): Set
    {
        $this->turn->switch();

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
        int $setsToPlay
    ): self {
        return new self($id, $player1, $player2, $setsToPlay);
    }
}
