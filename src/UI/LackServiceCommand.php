<?php

declare(strict_types=1);

namespace Tennis\UI;

class LackServiceCommand extends Command
{
    public function execute(?string $args = null): void
    {
        $this->game->getMatch()->lackService();
        $this->game->drawScoreboard();
    }
}
