<?php

declare(strict_types=1);

namespace Tennis\UI;

use Tennis\TennisController;

abstract class Command
{
    protected array $matches = [];
    protected string $validationPattern = '';
    protected string $validationMessage = '';

    public function __construct(
        protected ViewIO $viewIO,
        protected TennisController $tennisController
    ) {}

    public function execute(?string $args = null): void
    {
        if (empty($args) || !preg_match($this->validationPattern, $args, $this->matches)) {
            $this->viewIO->writeLine($this->validationMessage);
            return;
        }

        $this->run();
    }

    protected abstract function run(): void;
}
