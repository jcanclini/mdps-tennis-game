<?php

declare(strict_types=1);

namespace Tennis\UI\Views;

use Tennis\TennisController;
use Tennis\UI\Commands\LackService;
use Tennis\UI\Commands\PointRest;
use Tennis\UI\Commands\PointService;
use Tennis\UI\ConsoleCommand;
use Tennis\UI\View;
use Tennis\UI\ViewIO;

class TennisMatch extends View
{
    protected Scoreboard $scoreBoard;

    public function __construct(ViewIO $viewIO, TennisController $tennisController)
    {
        parent::__construct($viewIO, $tennisController, [
            ConsoleCommand::LACK_SERVICE->value => LackService::class,
            ConsoleCommand::POINT_SERVICE->value => PointService::class,
            ConsoleCommand::POINT_REST->value => PointRest::class,
        ]);
        $this->scoreBoard = new Scoreboard($viewIO, $tennisController);
        $this->prompt = "match id:{$tennisController->getScoreboard()->getMatchId()}>";
    }

    public function render(): void
    {
        do {
            $this->executeCommand();
            $this->scoreBoard->render();
        } while ($this->tennisController->getScoreboard()->isMatchFinished() === false);
    }
}
