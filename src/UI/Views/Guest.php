<?php

declare(strict_types=1);

namespace Tennis\UI\Views;

use Tennis\TennisController;
use Tennis\UI\Commands\CreateReferee;
use Tennis\UI\Commands\Help;
use Tennis\UI\Commands\Login;
use Tennis\UI\Commands\Simulation;
use Tennis\UI\ConsoleCommand;
use Tennis\UI\View;
use Tennis\UI\ViewIO;

class Guest extends View
{
    public function __construct(ViewIO $viewIO, TennisController $tennisController)
    {
        parent::__construct($viewIO, $tennisController, [
            ConsoleCommand::CREATE_REFEREE->value => CreateReferee::class,
            ConsoleCommand::LOGIN->value => Login::class,
            ConsoleCommand::SIMULATION->value => Simulation::class,
            ConsoleCommand::HELP->value => Help::class
        ]);
    }

    public function render(): void
    {
        do {
            $command = $this->executeCommand();
        } while ($command !== ConsoleCommand::EXIT);
    }
}
