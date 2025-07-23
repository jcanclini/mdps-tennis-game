<?php

declare(strict_types=1);

namespace Tennis\UI;

use Tennis\TennisController;

abstract class Command
{
    public function __construct(
        protected ViewIO $viewIO,
        protected TennisController $tennisController
    ) {}

    public abstract function execute(?string $args = null): void;
}