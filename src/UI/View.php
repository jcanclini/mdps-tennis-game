<?php

namespace Tennis\UI;

use Tennis\TennisController;

abstract class View
{
    protected string $prompt = "> ";

    protected ViewIO $viewIO;

    public function __construct(
        protected TennisController $tennisController,
        protected array $commands = []
    ) {
        $this->viewIO = new ViewIO();
        $this->commands[ConsoleCommand::EXIT->value] = ConsoleCommand::EXIT->value;
    }

    protected function executeCommand(): ConsoleCommand
    {
        [$command, $args] = $this->readCommand();
        new ($this->commands[$command->value])($this->viewIO, $this->tennisController)->execute($args);
        return $command;
    }

    protected function readCommand()
    {
        do {
            $inputs = explode(' ', trim($this->viewIO->read($this->prompt)), 2);
            $inputCommand = $this->parseCommand($inputs[0]);
            if ($inputCommand !== null) {
                return [$inputCommand, $inputs[1] ?? ""];
            }
            $this->viewIO->writeLine("Unknown command: {$inputs[0]}");
        } while ($inputCommand === null);
    }

    protected function parseCommand(string $commandName): ?ConsoleCommand
    {
        return array_key_exists($commandName, $this->commands) ? ConsoleCommand::from($commandName) : null;
    }

    protected function printBoxedMessage(string $message): void
    {
        $border = str_repeat('*', strlen($message) + 4);

        foreach ([$border, "* $message *", $border] as $line) {
            $this->viewIO->writeLine($line);
        }
    }

    abstract public function render(): void;
}
