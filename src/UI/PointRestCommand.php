<?php

declare(strict_types=1);

namespace Tennis\UI;

class PointRestCommand extends Command
{
    protected $requireLogin = true;

    public function execute(?string $args = null): void
    {
        $this->console->getGame()->currentMatch()->addPointToRest();
        $this->console->draw();;
    }
}
