<?php

declare(strict_types=1);

namespace Tennis\UI\Views;

use Tennis\TennisController;
use Tennis\UI\Commands\LackService;
use Tennis\UI\Commands\PointRest;
use Tennis\UI\Commands\PointService;
use Tennis\UI\ConsoleCommand;
use Tennis\UI\View;

class TennisMatch extends View
{
    protected Scoreboard $scoreBoard;

    public function __construct(TennisController $tennisController)
    {
        parent::__construct($tennisController, [
            ConsoleCommand::LACK_SERVICE->value => LackService::class,
            ConsoleCommand::POINT_SERVICE->value => PointService::class,
            ConsoleCommand::POINT_REST->value => PointRest::class,
        ]);
        $this->scoreBoard = new Scoreboard($tennisController);
        $this->prompt = "match id:{$tennisController->getScoreboard()->getMatchId()}>";
    }

    public function render(): void
    {
        do {
            $this->executeCommand();
            echo "Rendering scoreboard...\n";
            $this->scoreBoard->render();
            echo "Waiting for next command...\n";
        } while ($this->tennisController->currentMatchIsFinished() === false);
    }
}
