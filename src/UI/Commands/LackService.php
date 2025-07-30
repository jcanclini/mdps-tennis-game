<?php

declare(strict_types=1);

namespace Tennis\UI\Commands;

use Tennis\UI\Command;

class LackService extends Command
{
    public function run(): void
    {
        $this->tennisController->lackService();
    }
}
