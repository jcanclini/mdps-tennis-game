<?php

declare(strict_types=1);

namespace Tennis\UI\Commands;

use Tennis\UI\Command;

class LackService extends Command
{
    public function execute(?string $args = null): void
    {
        $this->tennisController->lackService();
    }
}
