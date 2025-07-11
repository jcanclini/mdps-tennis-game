<?php

declare(strict_types=1);

namespace Tennis;

class Set
{
    const MIN_GAMES_TO_WIN = 6;
    const MIN_GAMES_FOR_TIEBREAK = 12;
    const MIN_POINT_DIFFERENCE = 2;

    private const PLAYER_1 = 0;
    private const PLAYER_2 = 1;

    /**
     * @var Game[]
     */
    private array $games = [];

    private Turn $turn;

    /**
     * 
     * @param int $id 
     * @param Player $player1 
     * @param Player $player2  
     */
    public function __construct(
        private readonly int $id,
        private readonly Player $player1,
        private readonly Player $player2
    ) {
        $this->turn = Turn::create($player1, $player2);
        $this->createGame();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTurn(): Turn
    {
        return $this->turn;
    }

    public function addPointToService(): void
    {
        assert($this->isFinished() === false, 'Cannot add point to service when the set is finished.');

        $this->currentGame()->addPointToService();
        if (!$this->isTieBreak() && $this->currentGame()->isFinished()) {
            $this->createGame();
        }
    }

    public function addPointToRest(): void
    {
        assert($this->isFinished() === false, 'Cannot add point to rest when the set is finished.');

        $this->currentGame()->addPointToRest();
        if (!$this->isTieBreak() && $this->currentGame()->isFinished()) {
            $this->createGame();
        }
    }

    public function isFinished(): bool
    {
        return $this->getWinner() !== null;
    }

    public function getWinner(): ?Player
    {
        if ($this->currentGame() instanceof TieBreak) {
            return $this->currentGame()->getWinner();
        }

        $gamesWon = $this->getGamesWon();

        if ($this->isWinner($gamesWon[self::PLAYER_1], $gamesWon[self::PLAYER_2])) {
            return $this->player1;
        }

        if ($this->isWinner($gamesWon[self::PLAYER_2], $gamesWon[self::PLAYER_1])) {
            return $this->player2;
        }

        return null;
    }

    private function isWinner(int $player1, int $player2): bool
    {
        return (
            $player1 >= self::MIN_GAMES_TO_WIN && $player1 - $player2 >= self::MIN_POINT_DIFFERENCE
        );
    }

    /**
     * @return Game[]
     */
    public function getGames(): array
    {
        return $this->games;
    }

    public function getCurrentGame(): Game
    {
        return $this->currentGame();
    }

    public function getPoints(): array
    {
        return $this->getGamesWon();
    }

    public function isSetBall(): bool
    {
        return ($this->hasSetBallOpportunity(self::PLAYER_1, self::PLAYER_2) ||
            $this->hasSetBallOpportunity(self::PLAYER_2, self::PLAYER_1)) &&
            $this->currentGame()->isGameBall();
    }

    private function hasSetBallOpportunity(int $playerIndex, int $opponentIndex): bool
    {
        $gamesWon = $this->getGamesWon();
        return $gamesWon[$playerIndex] >= self::MIN_GAMES_TO_WIN - 1
            && $gamesWon[$opponentIndex] < self::MIN_GAMES_TO_WIN - 1;
    }

    public function isTieBreak(): bool
    {
        return $this->currentGame() instanceof TieBreak;
    }

    private function hasMinGamesToWin($player): bool
    {
        $gamesWon = $this->getGamesWon();
        return $gamesWon[$player] === self::MIN_GAMES_TO_WIN;
    }

    private function getGamesWon(): array
    {
        $gamesWon = [self::PLAYER_1 => 0, self::PLAYER_2 => 0];

        foreach ($this->games as $game) {
            if (!$game->isFinished()) {
                continue;
            }

            if ($game->getWinner() === $this->player1) {
                $gamesWon[self::PLAYER_1]++;
            } else {
                $gamesWon[self::PLAYER_2]++;
            }
        }

        return $gamesWon;
    }

    private function currentGame(): Game
    {
        return end($this->games);
    }

    private function createGame(): void
    {
        $this->turn->switch();

        if ($this->asdfadfsfaf()) {
            $this->games[] = TieBreak::create(count($this->games) + 1, $this->turn);
            return;
        }


        $this->games[] = Game::create(count($this->games) + 1, $this->turn);
    }

    private function asdfadfsfaf(): bool
    {
        return count($this->games) >= self::MIN_GAMES_FOR_TIEBREAK &&
            $this->hasMinGamesToWin(self::PLAYER_1) &&
            $this->hasMinGamesToWin(self::PLAYER_2);
    }

    public static function create(int $id, Player $service, Player $rest): self
    {
        return new self($id, $service, $rest);
    }
}
