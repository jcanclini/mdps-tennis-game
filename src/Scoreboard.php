<?php

declare(strict_types=1);

namespace Tennis;

class Scoreboard
{
    private Turn $turn;
    private array $points = [];
    private array $sets = [];
    private int $pendingSets = 0;
    private bool $isLackService = false;
    private bool $isGameBall = false;
    private bool $isSetBall = false;
    private bool $isTieBreak = false;
    private bool $isMatchBall = false;
    private bool $isMatchFinished = false;
    private TennisMatch $currentMatch;
    private Referee $referee;

    public function __construct(
        array $points,
        bool $isLackService = false,
        bool $isGameBall = false,
    ) {
        $this->points = $points;
        $this->isLackService = $isLackService;
        $this->isGameBall = $isGameBall;
    }

    public function setCurrentMatch(TennisMatch $match): self
    {
        $this->currentMatch = $match;
        return $this;
    }

    public function setTurn(Turn $turn): self
    {
        $this->turn = $turn;
        return $this;
    }

    public function setReferee(Referee $referee): self
    {
        $this->referee = $referee;
        return $this;
    }

    public function setSets(array $sets): self
    {
        $this->sets = $sets;
        return $this;
    }

    public function setPendingSets(int $pendingSets): self
    {
        $this->pendingSets = $pendingSets;
        return $this;
    }

    public function setSetBall(bool $isSetBall): self
    {
        $this->isSetBall = $isSetBall;
        return $this;
    }

    public function setTieBreak(bool $isTieBreak): self
    {
        $this->isTieBreak = $isTieBreak;
        return $this;
    }

    public function setMatchBall(bool $isMatchBall): self
    {
        $this->isMatchBall = $isMatchBall;
        return $this;
    }

    public function setMatchFinished(bool $isMatchFinished): self
    {
        $this->isMatchFinished = $isMatchFinished;
        return $this;
    }

    public function getCurrentMatch(): ?TennisMatch
    {
        return $this->currentMatch;
    }

    public function getMatchId(): int
    {
        return $this->currentMatch->getId();
    }

    public function getReferee(): Referee
    {
        return $this->referee;
    }

    /**
     * Get the current score of the match.
     * 
     * @return array<int, Set>
     */
    public function getSets(): array
    {
        return $this->sets;
    }

    public function getCurrentSet(): Set
    {
        return $this->sets[count($this->sets) - 1];
    }

    public function getPendingSets(): int
    {
        return $this->pendingSets;
    }

    public function getService(): Player
    {
        return $this->turn->getService();
    }

    public function getPoints(): array
    {
        return $this->points;
    }

    public function getPlayers(): array
    {
        return $this->turn->getPlayers();
    }

    public function isLackService(): bool
    {
        return $this->isLackService;
    }

    public function isGameBall(): bool
    {
        return $this->isGameBall;
    }

    public function isSetBall(): bool
    {
        return $this->isSetBall;
    }

    public function isTieBreak(): bool
    {
        return $this->isTieBreak;
    }

    public function isMatchBall(): bool
    {
        return $this->isMatchBall;
    }

    public function isMatchFinished(): bool
    {
        return $this->isMatchFinished;
    }

    public function getPointsDifference(): int
    {
        return $this->points[$this->getPlayers()[0]->getId()] - $this->points[$this->getPlayers()[1]->getId()];
    }
}
