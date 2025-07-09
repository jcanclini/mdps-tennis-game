<?php

declare(strict_types=1);

namespace Tennis\UI;

use Tennis\TennisGame;

abstract class Command
{
    protected TennisGame $game;

    public function __construct(TennisGame $game)
    {
        $this->game = $game;
    }

    public abstract function execute(?string $args = null): void;

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
