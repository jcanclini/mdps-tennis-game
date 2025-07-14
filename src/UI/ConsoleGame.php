<?php

declare(strict_types=1);

namespace Tennis\UI;

use Tennis\Scoreboard;
use Tennis\TennisGame;

class ConsoleGame
{
    private TennisGame $game;

    private const COMMANDS = [
        ConsoleCommand::CREATE_REFEREE->value => CreateRefereeCommand::class,
        ConsoleCommand::LOGIN->value => LoginCommand::class,
        ConsoleCommand::CREATE_PLAYER->value => CreatePlayerCommand::class,
        ConsoleCommand::READ_PLAYERS->value => ReadPlayersCommand::class,
        ConsoleCommand::CREATE_MATCH->value => CreateMatchCommand::class,
        ConsoleCommand::LACK_SERVICE->value => LackServiceCommand::class,
        ConsoleCommand::POINT_SERVICE->value => PointServiceCommand::class,
        ConsoleCommand::POINT_REST->value => PointRestCommand::class,
        ConsoleCommand::LOGOUT->value => LogoutCommand::class,
        ConsoleCommand::HELP->value => HelpCommand::class,
    ];

    public function __construct()
    {
        $this->game = new TennisGame(new Scoreboard());
    }

    public function getGame(): TennisGame
    {
        return $this->game;
    }

    public function start(): void
    {
        $this->println("Welcome to the Tennis Game Console!");
        $this->println();

        $this->game->createReferee('molina', '1234');
        $this->game->login('molina', '1234');
        $this->game->createPlayer('Nadal');
        $this->game->createPlayer('Alcaraz');
        $this->game->createPlayer('Zapata');

        $this->game->createMatch(
            $this->game->getPlayer(1),
            $this->game->getPlayer(2),
            3
        );

        $this->println(">createMatch sets:3;ids:1,2");
        $this->println("id: {$this->game->getMatchId()}");
        $this->println("date: {$this->game->getMatchDate()}");
        $this->println("Referee: {$this->game->getRefereeName()}");
        $this->println();
        $this->draw();

        do {
            [$command, $args] = $this->readCommand();

            assert(array_key_exists($command->value, self::COMMANDS), "Command: {$command->value} not found in commands list");

            $commandInstance = new (self::COMMANDS[$command->value])($this);

            assert($commandInstance instanceof Command, "Command class must extend Command");

            $commandInstance($args);
        } while ($command !== ConsoleCommand::LOGOUT);
    }

    /**
     * Reads a command from the console input.
     *
     * @return array<int,<Command, string>>
     */
    private function readCommand(): array
    {
        $prompt = ">";
        if ($this->game->getMatch()) {
            $prompt = "match id:{$this->game->getMatchId()}>";
        }

        while (true) {

            [$inputCommand, $args] = $this->readInput($prompt);

            if (!ConsoleCommand::hasCommand($inputCommand)) {
                $this->println("Unknown command: $inputCommand");
                continue;
            }

            return [ConsoleCommand::from($inputCommand), $args];
        }
    }

    public function draw(): void
    {
        if (empty($this->game->getMatch())) {
            $this->println("No matches available.");
            return;
        }

        [$player1, $player2] = $this->game->getMatch()->getPlayers();

        if ($this->game->getMatch()->getCurrentGameService() === $player1) {
            $scorePlayer1 = $this->game->getMatch()->hasLackService() ? "+ " : "* ";
            $scorePlayer2 = "  ";
        } else {
            $scorePlayer1 = "  ";
            $scorePlayer2 = $this->game->getMatch()->hasLackService() ? "+ " : "* ";
        }

        [$score1, $score2] = $this->game->getScore($player1, $player2);

        $biggerName = max(strlen($player1->getName()), strlen($player2->getName()));

        $scorePlayer1 .= str_pad($player1->getName(), $biggerName, " ", STR_PAD_RIGHT) . ": {$score1}";
        $scorePlayer2 .= str_pad($player2->getName(), $biggerName, " ", STR_PAD_RIGHT) . ": {$score2}";

        foreach ($this->game->getMatch()->getSets() as $set) {
            $gamesWon = $set->getPoints();
            $player1Points = $gamesWon[0];
            $player2Points = $gamesWon[1];

            $scorePlayer1 .= $player1Points ? " {$player1Points}" : " -";
            $scorePlayer2 .= $player2Points ? " {$player2Points}" : " -";
        }

        for ($i = 0; $i < $this->game->getMatch()->getPendingSets(); $i++) {
            $scorePlayer1 .= " -";
            $scorePlayer2 .= " -";
        }

        $this->println($scorePlayer1);
        $this->println($scorePlayer2);

        if ($this->game->getMatch()->isGameBall()) {
            $this->println();
            $this->printBoxedMessage("Game Ball!!!");
            $this->println();
        }
        if ($this->game->getMatch()->isSetBall()) {
            $this->printBoxedMessage("Set Ball!!!");
            $this->println();
        }
        if ($this->game->getMatch()->isTieBreak()) {
            $this->println();
            $this->printBoxedMessage("Tie Break!!!");
            $this->println();
        }
    }

    public function readInput(string $prompt): array
    {
        $input = trim(readline($prompt));
        if (empty($input)) {
            return ["", ""];
        }

        $parts = explode(' ', $input, 2);
        $command = $parts[0];
        $args = isset($parts[1]) ? $parts[1] : "";

        return [$command, $args];
    }

    public function println(string $message = ""): void
    {
        echo $message . PHP_EOL;
    }

    public function printBoxedMessage(string $message): void
    {
        $length = strlen($message);
        $border = str_repeat('*', $length + 4);

        echo $border . PHP_EOL;
        echo "* $message *" . PHP_EOL;
        echo $border . PHP_EOL;
    }
}
