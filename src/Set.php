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
        if (!$this->isFinished() && !$this->isTieBreak() && $this->getCurrentGame()->isFinished()) {
            $this->createGame();
        }
    }

    public function lackService(): void
    {
        if (!$this->isFinished() && !$this->isTieBreak() && $this->getCurrentGame()->isFinished()) {
            $this->createGame();
        }
    }

    public function getScoreboard(): Scoreboard
    {
        return $this->getCurrentGame()
            ->getScoreboard()
            ->setSetBall($this->isSetBall())
            ->setTieBreak($this->isTieBreak());
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

    public function getGamesWonBy(Player $player): int
    {
        return count(array_filter($this->games, fn(Game $game) => $game->isWinner($player)));
    }

    public function isWinner(Player $player): bool
    {
        return (
            ($this->isTieBreak() && $this->getCurrentGame()->isWinner($player)) ||
            ($this->hasMinimumGamesWon($player, self::MIN_GAMES_TO_WIN) &&
                $this->hasMinimumPointDifference($player, self::MIN_POINT_DIFFERENCE))
        );
    }

    private function isTieBreak(): bool
    {
        return count($this->games) === self::MIN_GAMES_FOR_TIEBREAK + 1;
    }

    private function isSetBall(): bool
    {
        return !empty(array_filter(
            $this->turn->getPlayers(),
            fn(Player $player) => $this->getCurrentGame()->isGameBallFor($player) &&
                $this->hasMinimumGamesWon($player, self::MIN_GAMES_TO_WIN - 1) &&
                $this->hasMinimumPointDifference($player, 1)
        ));
    }

    private function hasMinimumGamesWon(Player $player, int $minimum): bool
    {
        return $this->getGamesWonBy($player) >= $minimum;
    }

    private function hasMinimumPointDifference(Player $player, int $minimum): bool
    {
        return $this->getGamesWonBy($player) - $this->getGamesWonBy($this->turn->getOpponent($player)) >= $minimum;
    }

    private function getCurrentGame(): Game
    {
        return $this->games[count($this->games) - 1];
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
        return $this->bothPlayersCanWinSet() &&
            count($this->games) === self::MIN_GAMES_FOR_TIEBREAK;
    }

    private function bothPlayersCanWinSet(): bool
    {
        return count(array_filter(
            $this->turn->getPlayers(),
            fn(Player $player) => $this->getGamesWonBy($player) === self::MIN_GAMES_TO_WIN
        )) === count($this->turn->getPlayers());
    }
}
