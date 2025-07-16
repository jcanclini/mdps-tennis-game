<?php

declare(strict_types=1);

namespace Tennis\UI;

abstract class Command
{
    protected ConsoleGame $console;
    protected $requireLogin = false;

    public function __construct(ConsoleGame $console)
    {
        $this->console = $console;
    }

    public function __invoke(?string $args = null): void
    {
        if ($this->requireLogin && !$this->isLoggedIn()) {
            $this->console->println("You must be logged in to execute this command.");
            return;
        }

        $this->execute($args);
    }

    protected abstract function execute(?string $args = null): void;

    protected function isLoggedIn(): bool
    {
        return $this->console->getGame()->isLoggedIn();
    }

    protected function isMatchInProgress(): bool
    {
        return $this->console->getGame()->currentMatch() !== null;
    }
}
