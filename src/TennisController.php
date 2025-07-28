<?php

declare(strict_types=1);

namespace Tennis;

class TennisController
{
    /**
     * @var array<int, Player>
     */
    private array $players = [];

    private Turn $turn;

    /**
     * @var array<int, Referee>
     */
    private array $referees = [];
    private ?int $loggedReferee = null;

    /**
     * @var array<int, TennisMatch>
     */
    private array $matches = [];

    public function __construct() {}

    public function createReferee(string $name, string $password): void
    {
        $this->referees[] = new Referee(count($this->referees) + 1, $name, $password);
    }

    public function login(string $name, string $password): bool
    {
        foreach ($this->referees as $key => $referee) {
            if ($referee->areCredentialsValid($name, $password)) {
                $this->loggedReferee = $key;
                return true;
            }
        }
        return false;
    }

    public function logout(): void
    {
        $this->loggedReferee = null;
    }

    public function isLoggedIn(): bool
    {
        return $this->loggedReferee !== null;
    }

    public function createPlayer(string $name): void
    {
        $this->players[] = new Player(count($this->players) + 1, $name);
    }

    /**
     * Create a match with the given players and sets to play.
     * 
     * @param array<int, Player> $players 
     * @param int $setsToPlay 
     * @return void 
     */
    public function createMatch(
        array $players,
        int $setsToPlay
    ): void {
        assert($this->loggedReferee !== null, 'Referee must be logged in to create a match.');

        $this->turn = Turn::createRandom($players);

        $this->matches[] = new TennisMatch(
            count($this->matches) + 1,
            $this->turn,
            $setsToPlay
        );
    }

    public function addPointToService(): void
    {
        assert($this->getCurrentMatch() !== null, 'No match created yet.');
        $this->getCurrentMatch()->addPointTo($this->turn->getService());
    }

    public function addPointToRest(): void
    {
        assert($this->getCurrentMatch() !== null, 'No match created yet.');
        $this->getCurrentMatch()->addPointTo($this->turn->getRest());
    }

    public function lackService(): void
    {
        assert($this->getCurrentMatch() !== null, 'No match created yet.');
        $this->getCurrentMatch()->lackService();
    }

    public function getScoreboard(): Scoreboard
    {
        assert($this->getCurrentMatch() !== null, 'No match created yet.');

        return $this->getCurrentMatch()
            ->getScoreboard()
            ->setTurn($this->turn)
            ->setReferee($this->referees[$this->loggedReferee])
            ->setCurrentMatch($this->getCurrentMatch());
    }

    /**
     * @return array<int, Player>
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    public function getPlayer(int $id): ?Player
    {
        return array_find($this->players, fn(Player $player) => $player->getId() === $id);
    }

    public function getCurrentMatch(): ?TennisMatch
    {
        return $this->matches[count($this->matches) - 1] ?? null;
    }
}
