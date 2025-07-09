<?php

declare(strict_types=1);

namespace Tennis;

class Set
{
    const MIN_GAMES_TO_WIN = 6;
    const MIN_POINT_DIFFERENCE = 2;

    private const PLAYER_1 = 0;
    private const PLAYER_2 = 1;

    private $points = [
        self::PLAYER_1 => 0,
        self::PLAYER_2 => 0,
    ];

    /**
     * @var Game[]
     */
    private array $games = [];

    public ?Player $winner = null;

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

    public function addPointToService(): void
    {
        $this->currentGame()->addPointToService();
        if ($this->currentGame()->isFinished()) {
            echo "Game finished. Checking status..." . PHP_EOL;
            $this->checkStatus();
        }
    }

    public function addPointToRest(): void
    {
        $this->currentGame()->addPointToRest();
        if ($this->currentGame()->isFinished()) {
            $this->checkStatus();
        }
    }

    private function checkStatus(): void
    {
        $this->currentGame()->getWinner() === $this->player1
            ? $this->points[self::PLAYER_1]++
            : $this->points[self::PLAYER_2]++;

        if ($this->winner = $this->getSetWinner()) {
            return;
        }

        $this->turn->switch();

        $this->createGame();
    }

    private function getSetWinner(): ?Player
    {
        if ($this->currentGame() instanceof TieBreak) {
            return $this->currentGame()->getWinner();
        }

        if (
            $this->points[self::PLAYER_1] >= self::MIN_GAMES_TO_WIN &&
            $this->points[self::PLAYER_1] - $this->points[self::PLAYER_2] >= self::MIN_POINT_DIFFERENCE
        ) {
            return $this->player1;
        }

        if (
            $this->points[self::PLAYER_2] >= self::MIN_GAMES_TO_WIN &&
            $this->points[self::PLAYER_2] - $this->points[self::PLAYER_1] >= self::MIN_POINT_DIFFERENCE
        ) {
            return $this->player2;
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

    public function getCurrentGame(): Game
    {
        return $this->currentGame();
    }

    public function getPoints(): array
    {
        return $this->points;
    }

    public function isSetBall(): bool
    {
        $servicePoints = $this->points[self::PLAYER_1];
        $restPoints = $this->points[self::PLAYER_2];

        return ($servicePoints >= 5 && $restPoints < 5) ||
            ($restPoints >= 5 && $servicePoints < 5) &&
            $this->currentGame()->isGameBall();
    }

    public function getWinner(): ?Player
    {
        return $this->winner;
    }

    public function isFinished(): bool
    {
        return $this->winner !== null;
    }

    private function currentGame(): Game
    {
        return end($this->games);
    }

    private function createGame(): void
    {
        if ($this->isTieBreak()) {
            $this->games[] = TieBreak::create(count($this->games) + 1, $this->turn);
            return;
        }

        $this->games[] = Game::create(count($this->games) + 1, $this->turn);
    }

    public function isTieBreak(): bool
    {
        return count($this->games) >= 12 &&
            $this->points[self::PLAYER_1] === self::MIN_GAMES_TO_WIN &&
            $this->points[self::PLAYER_2] === self::MIN_GAMES_TO_WIN;
    }

    public static function create(int $id, Player $service, Player $rest): self
    {
        return new self($id, $service, $rest);
    }
}
