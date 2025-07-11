<?php

declare(strict_types=1);

namespace Tennis\UI;

use Tennis\Scoreboard;
use Tennis\TennisGame;
use Tennis\TennisMatch;

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

            assert(array_key_exists($command->value, self::COMMANDS), "Command: {$command->value} not found in commands list");

            $commandInstance = new (self::COMMANDS[$command->value])($this->game);

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

    private function readInput(string $prompt): array
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
