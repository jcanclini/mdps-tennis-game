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
        foreach ($this->turn->getPlayers() as $player) {
            if ($this->isWinner($player)) {
                return true;
            }
        }
        return false;
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
        return count($this->games) === self::MIN_GAMES_FOR_TIEBREAK + 1;
    }

    public function isSetBall(): bool
    {
        return $this->hasSetBallOpportunity() && $this->getCurrentGame()->isGameBall();
    }

    public function isWinner(Player $player): bool
    {
        return (
            ($this->isTieBreak() && $this->getCurrentGame()->isWinner($player))
            ||
            ($this->getGamesWonBy($player) >= self::MIN_GAMES_TO_WIN &&
                $this->getGamesWonBy($player) - $this->getGamesWonBy($this->turn->getOpponent($player)) >= self::MIN_POINT_DIFFERENCE)
        );
    }

    private function hasSetBallOpportunity(): bool
    {
        foreach ($this->turn->getPlayers() as $player) {
            if (
                $this->getGamesWonBy($player) === self::MIN_GAMES_TO_WIN - 1 &&
                $this->getGamesWonBy($this->turn->getOpponent($player)) < self::MIN_GAMES_TO_WIN - 1
            ) {
                return true;
            }
        }

        return false;
    }

    private function getGamesWonBy(Player $player): int
    {
        return count(array_filter($this->games, fn(Game $game) => $game->isWinner($player)));
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
        return count($this->games) === self::MIN_GAMES_FOR_TIEBREAK &&
            $this->hasMinGamesToWin($this->turn->getPlayers()[0]) &&
            $this->hasMinGamesToWin($this->turn->getPlayers()[1]);
    }

    private function hasMinGamesToWin(Player $player): bool
    {
        return $this->getGamesWonBy($player) === self::MIN_GAMES_TO_WIN;
    }
}
