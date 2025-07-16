<?php

declare(strict_types=1);

namespace Tennis;

class TennisController
{
    /**
     * @var array<int, Player>
     */
    private array $players = [];

    /**
     * @var array<int, Referee>
     */
    private array $referees = [];
    private ?int $loggedReferee = null;

    /**
     * @var array<int, TennisMatch>
     */
    private array $matches = [];

    public function __construct(
        private readonly Scoreboard $scoreboard
    ) {}

    public function createReferee(string $name, string $password): void
    {
        $this->referees[] = new Referee(count($this->referees) + 1, $name, $password);
    }

    public function login(string $name, string $password): void
    {
        foreach ($this->referees as $key => $referee) {
            if ($referee->areCredentialsValid($name, $password)) {
                $this->loggedReferee = $key;
            }
        }
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

    public function createMatch(
        Player $player1,
        Player $player2,
        int $setsToPlay
    ): void {
        assert($this->loggedReferee !== null, 'You must be logged in to create a match.');

        $this->matches[] = new TennisMatch(
            count($this->matches) + 1,
            $player1,
            $player2,
            $setsToPlay
        );

        $this->scoreboard->setMatch($this->currentMatch());
    }

    /**
     * @return array<int, Player>
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    public function getReferee(int $id): ?Referee
    {
        return $this->referee[$id] ?? null;
    }

    public function getPlayer(int $id): ?Player
    {
        return $this->players[$id] ?? null;
    }

    public function getScore(): array
    {
        return $this->scoreboard->getScore();
    }

    public function getBoard(): Scoreboard
    {
        return $this->scoreboard;
    }

    public function currentMatch(): ?TennisMatch
    {
        return end($this->matches) ?: null;
    }

    public function getMatchId(): int
    {
        assert($this->currentMatch() !== null, 'No match created yet.');

        return $this->currentMatch()->getId();
    }

    public function getMatchDate(): string
    {
        assert($this->currentMatch() !== null, 'No match created yet.');

        return $this->currentMatch()->getDate()->format('Y-m-d H:i:s');
    }

    public function getRefereeName(): string
    {
        assert($this->loggedReferee !== null, 'No referee logged in.');

        return $this->referees[$this->loggedReferee]->getName();
    }
}
