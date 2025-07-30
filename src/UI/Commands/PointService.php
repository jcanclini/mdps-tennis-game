<?php

declare(strict_types=1);

namespace Tennis\UI\Commands;

use Tennis\UI\Command;

class PointService extends Command
{
    public function run(): void
    {
        $this->tennisController->addPointToService();
    }
}
