<?php

declare(strict_types=1);

namespace Tennis\UI\Views;

use Tennis\TennisController;
use Tennis\UI\Commands\CreateMatch;
use Tennis\UI\Commands\CreatePlayer;
use Tennis\UI\Commands\Logout;
use Tennis\UI\Commands\ReadPlayers;
use Tennis\UI\ConsoleCommand;
use Tennis\UI\View;

class Authenticated extends View
{
    public function __construct(TennisController $tennisController)
    {
        parent::__construct($tennisController, [
            ConsoleCommand::CREATE_PLAYER->value => CreatePlayer::class,
            ConsoleCommand::READ_PLAYERS->value => ReadPlayers::class,
            ConsoleCommand::CREATE_MATCH->value => CreateMatch::class,
            ConsoleCommand::LOGOUT->value => Logout::class,
        ]);
    }

    public function render(): void
    {
        do {
            $command = $this->executeCommand();
        } while ($command !== ConsoleCommand::LOGOUT);
    }
}
