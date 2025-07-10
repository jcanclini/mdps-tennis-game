<?php

namespace Tennis\UI;

class LogoutCommand extends Command
{
    public  function execute(?string $args = null): void
    {
        $this->game->logout();
    }
}
