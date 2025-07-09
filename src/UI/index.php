<?php

declare(strict_types=1);

assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_EXCEPTION, 1);

use Tennis\UI\ConsoleGame;

require_once __DIR__ . '/../../vendor/autoload.php';

$consoleGame = new ConsoleGame();
$consoleGame->start();
