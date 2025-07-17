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

it('has a valid initial state on creation', function () {
    $match = createMatch();

    expect($match->getSets())->toHaveCount(1);
    expect($match->isFinished())->toBeFalse();
});

it('player 1 won the first set', function () {
    $player1 = createPlayer('Nadal');
    $match = createMatch(1, $player1);

    for ($m = 0; $m < $match->getMinSetsToWin(); $m++) {
        for ($i = 0; $i < Set::MIN_GAMES_TO_WIN; $i++) {
            simulateGameWinFromMatch($match, $player1);
        }
    }

    for ($s = 0; $s < $match->getMinSetsToWin(); $s++) {
        for ($g = 0; $g < Set::MIN_GAMES_TO_WIN; $g++) {
            expect($match->getSets()[$s]->getGames()[$g]->isWinner($player1))->toBeTrue();
        }
    }

    expect($match->isWinner($player1))->toBeTrue();
});

it('is match ball when player is ', function () {
    $player1 = new Player(1, 'Nadal');
    $match = createMatch(1, $player1);
    $score = new Scoreboard();
    $score->setMatch($match);

    simulateSetGamesWon($match, $player1, 11);

    simulateGamePointsWon($match, $player1, Game::MIN_POINTS_TO_WIN - 1);

    expect($match->isMatchBall())->toBeTrue();
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
    expect($match->isTieBreak())->toBeTrue();
});
