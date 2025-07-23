<?php

use Tennis\Game;
use Tennis\Player;
use Tennis\Scoreboard;
use Tennis\Set;

function simulateGameWinFromMatch(\Tennis\TennisMatch $match, \Tennis\Player $player): void
{
    for ($i = 0; $i < \Tennis\Game::MIN_POINTS_TO_WIN; $i++) {
        $match->addPointTo($player);
    }
}

it('get score board', function () {
    $player1 = new Player(1, 'Nadal');
    $player2 = new Player(2, 'Federer');
    $match = createMatch(1, $player1, $player2);

    expect($match->getScoreboard())->toBeInstanceOf(Scoreboard::class);
});

it('has a valid initial state on creation', function () {
    $match = createMatch();

    expect($match->getSets())->toHaveCount(1);
    expect($match->getScoreboard()->isMatchFinished())->toBeFalse();
});

it('player 1 won the first set', function () {
    $player1 = createPlayer('Nadal');
    $match = createMatch(1, $player1);

    for ($m = 0; $m < $match->getMinSetsToWin(); $m++) {
        for ($i = 0; $i < Set::MIN_GAMES_TO_WIN; $i++) {
            simulateGameWinFromMatch($match, $player1);
        }
    }

    expect($match->isWinner($player1))->toBeTrue();
});

it('is match ball when player is ', function () {
    $service = new Player(1, 'Nadal');
    $match = createMatch(1, $service);

    simulateSetGamesWon($match, $service, 11);

    simulateGamePointsWon($match, $service, Game::MIN_POINTS_TO_WIN - 1);

    expect($match->getScoreboard()->isMatchBall())->toBeTrue();
});

it('simulate tie break', function () {
    $player1 = new Player(1, 'Nadal');
    $player2 = new Player(2, 'Federer');
    $match = createMatch(1, $player1, $player2);

    for ($i = 0; $i < 6; $i++) {
        simulateSetGamesWon($match, $player1, 1);
        simulateSetGamesWon($match, $player2, 1);
    }

    expect($match->getSets())->toHaveCount(1);
});
