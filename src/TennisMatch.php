<?php

declare(strict_types=1);

namespace Tennis;

use DateTimeImmutable;

class TennisMatch
{
    const ALLOWED_SETS = [3, 5];

    /**
     * @var array<int, Set>
     */
    private array $sets = [];

    private \DateTimeImmutable $date;

    public function __construct(
        private readonly int $id,
        private Turn $turn,
        private readonly int $setsToPlay
    ) {
        assert(in_array($setsToPlay, self::ALLOWED_SETS), 'Max sets must be either 3 or 5.');
        $this->createSet();
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

    public function addPointTo(Player $player): void
    {
        assert($this->isFinished() === false, 'Match is already finished.');
        $this->currentSet()->addPointTo($player);
        if (!$this->isFinished() && $this->currentSet()->isFinished()) {
            $this->createSet();
        }
    }

    public function lackService(): void
    {
        assert($this->isFinished() === false, 'Match is already finished.');
        $this->currentSet()->lackService();
        if (!$this->isFinished() && $this->currentSet()->isFinished()) {
            $this->createSet();
        }
    }

    public function getScoreboard(): Scoreboard
    {
        return $this->currentSet()
            ->getScoreboard()
            ->setSets($this->sets)
            ->setMatchFinished($this->isFinished())
            ->setPendingSets($this->getPendingSets())
            ->setMatchBall($this->isMatchBall());
    }

    private function getPendingSets(): int
    {
        return $this->setsToPlay - count($this->sets);
    }

    
    private function isMatchBall(): bool
    {
        return count($this->sets) >= $this->getMinSetsToWin() && $this->currentSet()->getScoreboard()->isSetBall();
    }

    private function isFinished(): bool
    {
        foreach ($this->turn->getPlayers() as $player) {
            if ($this->isWinner($player)) {
                return true;
            }
        }

        return false;
    }

    private function isWinner(Player $player): bool
    {
        return $this->getMinSetsToWin() === count(array_filter($this->sets, fn(Set $set) => $set->isWinner($player)));
    }

    private function getMinSetsToWin(): int
    {
        return intdiv($this->setsToPlay, 2) + 1;
    }

    private function createSet(): void
    {
        $this->sets[] = new Set(
            rand(1, 1000),
            $this->turn
        );
    }

    private function currentSet(): Set
    {
        return $this->sets[count($this->sets) - 1];
    }
}
