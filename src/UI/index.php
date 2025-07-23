<?php

declare(strict_types=1);

use Tennis\TennisController;
use Tennis\UI\Views\Guest;

assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_EXCEPTION, 1);

require_once __DIR__ . '/../../vendor/autoload.php';

// $consoleGame = new ConsoleGame();
// $consoleGame->start();
$controller = new TennisController();
$controller->createReferee('molina', '1234');
$controller->createPlayer('Nadal');
$controller->createPlayer('Federer');
new Guest($controller)->render();

// new GuestCommand(new TennisController())->execute();

// $controller = new TennisController();
// $controller->createReferee('referee', 'referee');
// $controller->login('referee', 'referee');
// $controller->createPlayer('Nadal');
// $controller->createPlayer('Federer');
// $controller->createMatch([$controller->getPlayer(1), $controller->getPlayer(2)], 3);
// new MatchCommand($controller)->execute();
