<?php

declare(strict_types=1);

namespace Tennis;

class Set
{
    const MIN_GAMES_TO_WIN = 6;
    const MIN_GAMES_FOR_TIEBREAK = 12;
    const MIN_POINT_DIFFERENCE = 2;

    /**
     * @var array<int, Game|TieBreak>
     */
    private array $games = [];

    public function __construct(
        private readonly int $id,
        private readonly Turn $turn
    ) {
        $this->createGame();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function addPointTo(Player $player): void
    {
        assert($this->isFinished() === false, 'Cannot add point to service when the set is finished.');

        $this->getCurrentGame()->addPointTo($player);
        if (!$this->isTieBreak() && $this->getCurrentGame()->isFinished()) {
            $this->createGame();
        }
    }

    public function isFinished(): bool
    {
        return $this->getWinner() !== null;
    }

    public function getWinner(): ?Player
    {
        if ($this->getCurrentGame() instanceof TieBreak) {
            return $this->getCurrentGame()->getWinner();
        }

        if ($this->isWinner($this->turn->getPlayer1())) {
            return $this->turn->getPlayer1();
        }

        if ($this->isWinner($this->turn->getPlayer2())) {
            return $this->turn->getPlayer2();
        }

        return null;
    }

    /**
     * @return Game[]
     */
    public function getGames(): array
    {
        return $this->games;
    }

    public function isTieBreak(): bool
    {
        return $this->getCurrentGame() instanceof TieBreak;
    }

    public function isSetBall(): bool
    {
        return ($this->hasSetBallOpportunity($this->turn->getPlayer1()) ||
            $this->hasSetBallOpportunity($this->turn->getPlayer2())) &&
            $this->getCurrentGame()->isGameBall();
    }

    private function isWinner(Player $player): bool
    {
        return (
            $this->getGamesWonBy($player) >= self::MIN_GAMES_TO_WIN &&
            $this->getGamesWonBy($player) - $this->getGamesWonBy($this->turn->getOpponent($player)) >= self::MIN_POINT_DIFFERENCE
        );
    }

    private function hasSetBallOpportunity(Player $player): bool
    {
        return $this->getGamesWonBy($player) >= self::MIN_GAMES_TO_WIN - 1
            && $this->getGamesWonBy($this->turn->getOpponent($player)) < self::MIN_GAMES_TO_WIN - 1;
    }

    private function hasMinGamesToWin(Player $player): bool
    {
        return $this->getGamesWonBy($player) === self::MIN_GAMES_TO_WIN;
    }

    public function getGamesWonBy(Player $player): int
    {
        return count(array_filter($this->games, fn(Game $game) => $game->isFinished() && $game->getWinner()->is($player)));
    }

    public function getCurrentGame(): Game
    {
        return end($this->games);
    }

    private function createGame(): void
    {
        if ($this->shouldPlayTieBreak()) {
            $this->games[] = new TieBreak(count($this->games) + 1, $this->turn);
            return;
        }

        $this->games[] = new Game(count($this->games) + 1, $this->turn);
    }

    private function shouldPlayTieBreak(): bool
    {
        return count($this->games) >= self::MIN_GAMES_FOR_TIEBREAK &&
            $this->hasMinGamesToWin($this->turn->getPlayer1()) &&
            $this->hasMinGamesToWin($this->turn->getPlayer2());
    }
}
