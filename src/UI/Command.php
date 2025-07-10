<?php

declare(strict_types=1);

namespace Tennis\UI;

use Tennis\TennisGame;

abstract class Command
{
    protected TennisGame $game;
    protected $requireLogin = false;

    public function __construct(TennisGame $game)
    {
        $this->game = $game;
    }

    public function __invoke(?string $args = null): void
    {
        if ($this->requireLogin && !$this->isLoggedIn()) {
            $this->println("You must be logged in to execute this command.");
            return;
        }

        $this->execute($args);
    }

    protected abstract function execute(?string $args = null): void;

    protected function isLoggedIn(): bool
    {
        return $this->game->isLoggedIn();
    }

    protected function isMatchInProgress(): bool
    {
        return $this->game->getMatch() !== null;
    }

    protected function print(string $message): void
    {
        echo $message;
    }

    protected function println(string $message = ""): void
    {
        echo $message . PHP_EOL;
    }

    protected function printBoxedMessage(string $message): void
    {
        $length = strlen($message);
        $border = str_repeat('*', $length + 4);

        echo $border . PHP_EOL;
        echo "* $message *" . PHP_EOL;
        echo $border . PHP_EOL;
    }
}
