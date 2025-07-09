<?php

declare(strict_types=1);

namespace Tennis\UI;

class PointRestCommand extends Command
{
    public function execute(?string $args = null): void
    {
        $this->game->getMatch()->addPointToRest();
        $this->game->drawScoreboard();
    }
}
