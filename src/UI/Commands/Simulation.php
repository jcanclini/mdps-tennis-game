<?php

declare(strict_types=1);

namespace Tennis\UI\Commands;

use Tennis\UI\Command;
use Tennis\UI\Views\Guest;

class Simulation extends Command
{
    public function __construct($_, $tennisController)
    {
        parent::__construct(new SimulationIO($tennisController), $tennisController);
    }

    public function run(): void
    {
        $guestView = new Guest($this->viewIO, $this->tennisController);
        $guestView->render();
    }
}
