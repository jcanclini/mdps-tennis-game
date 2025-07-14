<?php

declare(strict_types=1);

namespace Tennis;

class TennisGame
{
    /**
     * @var array<int, Player>
     * @psalm-var array<int, Player>
     */
    private array $players = [];

    /**
     * @var array<int, Referee>
     * @psalm-var array<int, Referee>
     */
    private array $referees = [];

    private ?TennisMatch $match = null;

    /**
     * @var array<int, TennisMatch>
     * @psalm-var array<int, TennisMatch>
     */
    private array $matches = [];

    private ?Referee $loggedUser = null;

    public function __construct(
        private readonly Scoreboard $scoreboard
    ) {}

    public function createReferee(string $name, string $password): void
    {
        $this->referees[count($this->referees) + 1] = Referee::create(count($this->referees) + 1, $name, $password);
    }

    public function login(string $name, string $password): void
    {
        foreach ($this->referees as $referee) {
            if ($referee->areCredentialsValid($name, $password)) {
                $this->loggedUser = $referee;
            }
        }
    }

    public function logout(): void
    {
        assert($this->loggedUser !== null, 'You must be logged in to log out.');
        $this->loggedUser = null;
    }

    public function isLoggedIn(): bool
    {
        return $this->loggedUser !== null;
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
        assert($this->loggedUser !== null, 'You must be logged in to create a match.');

        $this->match = TennisMatch::create(
            count($this->matches) + 1,
            $player1,
            $player2,
            $setsToPlay
        );

        $this->scoreboard->setMatch($this->match);
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

    public function getMatch(): ?TennisMatch
    {
        return $this->match;
    }

    public function getMatchId(): int
    {
        assert($this->match !== null, 'No match created yet.');

        return $this->match->getId();
    }

    public function getMatchDate(): string
    {
        assert($this->match !== null, 'No match created yet.');

        return $this->match->getDate()->format('Y-m-d H:i:s');
    }

    public function getRefereeName(): string
    {
        assert($this->loggedUser !== null, 'No referee logged in.');

        return $this->loggedUser->getName();
    }
}
