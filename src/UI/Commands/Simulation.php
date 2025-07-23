<?php

declare(strict_types=1);

namespace Tennis\UI\Commands;

use Tennis\UI\Command;
use Tennis\UI\Views\Guest;

class Simulation extends Command
{
    protected const COMMANDS = [
        PointService::class,
        PointRest::class,
    ];

    public function __construct($_, $tennisController)
    {
        parent::__construct(new SimulationIO(), $tennisController);
    }

    public function execute(?string $args = null): void
    {
        $guestView = new Guest($this->tennisController);
        $guestView->setIO($this->viewIO);
        $guestView->render();

        // $commandsNames = [
        //     PointService::class => 'pointService',
        //     PointRest::class => 'pointRest',
        // ];

        // do {
        //     $command = self::COMMANDS[rand(0, count(self::COMMANDS) - 1)];
        //     $this->viewIO->writeLine("match id:1>{$commandsNames[$command]}");
        //     (new $command($this->viewIO, $this->tennisController))->execute();
        //     sleep(1);
        // } while (!$this->tennisController->currentMatchIsFinished());
    }
}
