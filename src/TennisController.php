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
        assert($this->loggedReferee !== null, 'You must be logged in to log out.');
        $this->loggedReferee = null;
    }

    public function isLoggedIn(): bool
    {
        return $this->loggedReferee !== null;
    }

    public function createPlayer(string $name): void
    {
        $player =  new Player(count($this->players) + 1, $name);
        $this->players[$player->getId()] = $player;
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
        assert($this->loggedReferee !== null, 'You must be logged in to create a match.');

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

    public function currentMatchIsFinished(): bool
    {
        assert($this->getCurrentMatch() !== null, 'No match created yet.');

        return $this->getCurrentMatch()->isFinished();
    }

    public function getPlayer(int $id): ?Player
    {
        return $this->players[$id] ?? null;
    }

    public function getMatchDate(): string
    {
        assert($this->getCurrentMatch() !== null, 'No match created yet.');

        return $this->getCurrentMatch()->getDate()->format('Y-m-d H:i:s');
    }

    public function getRefereeName(): string
    {
        assert($this->loggedReferee !== null, 'No referee logged in.');

        return $this->referees[$this->loggedReferee]->getName();
    }

    private function getCurrentMatch(): ?TennisMatch
    {
        return $this->matches[count($this->matches) - 1] ?? null;
    }
}
