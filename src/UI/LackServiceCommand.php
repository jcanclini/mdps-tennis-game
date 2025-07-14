<?php

declare(strict_types=1);

namespace Tennis\UI;

class LackServiceCommand extends Command
{
    protected $requireLogin = true;

    public function execute(?string $args = null): void
    {
        $this->console->getGame()->getMatch()->lackService();
        $this->console->draw();;
    }
}
