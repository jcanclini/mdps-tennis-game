<?php

use Tennis\Player;
use Tennis\Set;

function simulateGameWinFromMatch(\Tennis\TennisMatch $match, \Tennis\Player $player): void
{
    for ($i = 0; $i < \Tennis\Game::MIN_POINTS_TO_WIN; $i++) {
        if ($match->getCurrentGameService() === $player) {
            $match->addPointToService();
        } else {
            $match->addPointToRest();
        }
    }
}

it('has a valid initial state on creation', function () {
    $match = createMatch();

    expect($match->getSets())->toHaveCount(1);
    expect($match->isFinished())->toBeFalse();
    expect($match->getWinner())->toBeNull();
});

it('player 1 won the first set', function () {
    $player1 = new Player(1, 'Nadal');
    $match = createMatch(1, $player1);

    for ($m = 0; $m < $match->getMinSetsToWin(); $m++) {
        for ($i = 0; $i < Set::MIN_GAMES_TO_WIN; $i++) {
            simulateGameWinFromMatch($match, $player1);
        }
    }

    for ($s = 0; $s < $match->getMinSetsToWin(); $s++) {
        for ($g = 0; $g < Set::MIN_GAMES_TO_WIN; $g++) {
            expect($match->getSets()[$s]->getGames()[$g]->getWinner()->getName())->toBe($player1->getName());
        }
    }

    expect($match->getWinner()->getName())->toBe('Nadal');
});
