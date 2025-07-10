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
    ];

    public function __construct()
    {
        $this->game = new TennisGame(new Scoreboard());
    }

    public function start(): void
    {
        $this->println("Welcome to the Tennis Game Console!");
        $this->println();

        $this->println("Available commands:");
        $this->println(">createReferee name:molina;password:1234");
        $this->println(">login name:molina;password:1234");
        $this->println(">createPlayer name:Nadal");
        $this->println(">createPlayer name:Alcaraz");
        $this->println(">createPlayer name:Zapata");
        $this->println(">readPlayers");
        $this->println(">createMatch sets:3;ids:1,2");
        $this->println(">lackService");
        $this->println(">pointService");
        $this->println(">pointRest");
        $this->println("-------------------------------------------");
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
        $this->game->drawScoreboard();

        do {
            [$command, $args] = $this->readCommand();

            if ($command === ConsoleCommand::LOGOUT) {
                $this->game->logout();
                return;
            }

            if (
                !in_array($command, [ConsoleCommand::LOGIN, ConsoleCommand::CREATE_REFEREE]) &&
                !$this->game->isLoggedIn()
            ) {
                $this->println("You must be logged in to execute this command.");
                continue;
            }

            match ($command) {
                ConsoleCommand::CREATE_REFEREE => new CreateRefereeCommand($this->game)->execute($args),
                ConsoleCommand::LOGIN => new LoginCommand($this->game)->execute($args),
                ConsoleCommand::CREATE_PLAYER => new CreatePlayerCommand($this->game)->execute($args),
                ConsoleCommand::READ_PLAYERS => new ReadPlayersCommand($this->game)->execute(),
                ConsoleCommand::CREATE_MATCH => new CreateMatchCommand($this->game)->execute($args),
                ConsoleCommand::LACK_SERVICE => new LackServiceCommand($this->game)->execute(),
                ConsoleCommand::POINT_SERVICE => new PointServiceCommand($this->game)->execute(),
                ConsoleCommand::POINT_REST => new PointRestCommand($this->game)->execute(),
                default => ""
            };
        } while ($command !== ConsoleCommand::LOGOUT);
    }

    private function readCommand(): array
    {
        $prompt = ">";
        if ($this->game->getMatch()) {
            $prompt = "match id:{$this->game->getMatchId()}>";
        }
        while (true) {

            $this->print($prompt);
            $input = trim(fgets(STDIN));

            [$inputCommand, $args] = explode(" ", $input) + [null, null];

            $command = ConsoleCommand::tryFrom(trim($inputCommand));

            if ($command !== null) {
                return [$command, $args];
            }
        }
    }

    private function print(string $message): void
    {
        echo $message;
    }

    private function println(string $message = ""): void
    {
        echo $message . PHP_EOL;
    }

    function printBoxedMessage(string $message): void
    {
        $length = strlen($message);
        $border = str_repeat('*', $length + 4);

        echo $border . PHP_EOL;
        echo "* $message *" . PHP_EOL;
        echo $border . PHP_EOL;
    }
}
