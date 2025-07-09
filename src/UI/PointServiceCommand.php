<?php

declare(strict_types=1);

namespace Tennis\UI;

class PointServiceCommand extends Command
{
    public function execute(?string $args = null): void
    {
        $this->game->getMatch()->addPointToService();
        $this->game->drawScoreboard();
    }
}
