<?php

use Tennis\Player;
use Tennis\Set;


function simulateTieBreakWin(Set $set, Player $player): void
{
    if ($set->getTurn()->isService($player)) {
        $set->addPointToService();
        $set->addPointToRest();
        $set->addPointToRest();
        $set->addPointToService();
        $set->addPointToService();
        $set->addPointToRest();
    } else {
        $set->addPointToRest();
        $set->addPointToService();
        $set->addPointToService();
        $set->addPointToRest();
        $set->addPointToRest();
        $set->addPointToService();
    }
}

it('has a valid initial state on creation', function () {
    $set = createSet();
    expect($set->getId())->toBe(1);
    expect($set->isFinished())->toBeFalse();
    expect($set->getWinner())->toBeNull();
    expect($set->getGames())->toHaveCount(1);
});

it('service won the first game', function () {
    $service = createPlayer();
    $set = createSet($service);

    simulateSetGameWin($set, $service);

    expect($set->getGames())->toHaveCount(2);
    expect($set->getGames()[0]->getWinner())->toBe($service);
});

it('rest won the set', function () {
    $service = createPlayer(1, 'Nadal');
    $rest = createPlayer(2, 'Federer');
    $set = createSet($service, $rest);

    simulateSetWin($set, $rest);

    expect($set->getWinner())->toBe($rest);
});

it('player 1 and player 2 are tied 1-1 before server won 2 games of the set', function () {
    $service = createPlayer(1, 'Nadal');
    $rest = createPlayer(2, 'Federer');
    $set = createSet($service, $rest);

    simulateSetGameWin($set, $service);
    simulateSetGameWin($set, $rest);

    expect($set->getGames()[0]->getWinner()->getName())->toBe($service->getName());
    expect($set->getGames()[1]->getWinner()->getName())->toBe($rest->getName());
});

describe('is set ball', function () {
    it('returns true when the service has 5 games won and 3 point in current game and the rest hass less than 5 sets won', function () {
        $service = createPlayer(1, 'Nadal');
        $set = createSet($service);

        for ($i = 0; $i < 5; $i++) {
            simulateSetGameWin($set, $service);
        }

        $set->addPointToService();
        $set->addPointToService();
        $set->addPointToService();
        expect($set->isSetBall())->toBeTrue();
    });

    it('returns false when the service has less than 5 sets won', function () {
        $service = createPlayer(1, 'Nadal');
        $set = createSet($service);

        for ($i = 0; $i < 4; $i++) {
            simulateSetGameWin($set, $service);
        }

        expect($set->isSetBall())->toBeFalse();
    });

    it('returns false when the service has 5 sets won but less than 3 points in current game', function () {
        $service = createPlayer(1, 'Nadal');
        $set = createSet($service);

        for ($i = 0; $i < 4; $i++) {
            simulateSetGameWin($set, $service);
        }

        $set->addPointToService();
        $set->addPointToService();
        $set->addPointToService();
        expect($set->isSetBall())->toBeFalse();
    });
});


describe('tie break', function () {
    it('is created when the set is tied at 6-6', function () {
        $service = createPlayer(1, 'Nadal');
        $rest = createPlayer(2, 'Federer');
        $set = createSet($service, $rest);


        for ($i = 0; $i < Set::MIN_GAMES_TO_WIN; $i++) {
            simulateSetGameWin($set, $service);
            simulateSetGameWin($set, $rest);
        }

        expect($set->getPoints())->toBe([6, 6]);
        expect($set->getGames())->toHaveCount(13);
        expect($set->isTieBreak())->toBeTrue();
    });

    it('service wins the set winning tie-break', function () {
        $service = createPlayer(1, 'Nadal');
        $rest = createPlayer(2, 'Federer');
        $set = createSet($service, $rest);

        for ($i = 0; $i < Set::MIN_GAMES_TO_WIN; $i++) {
            simulateSetGameWin($set, $service);
            simulateSetGameWin($set, $rest);
        }

        expect($set->isTieBreak())->toBeTrue();

        simulateTieBreakWin($set, $service);

        expect($set->isFinished())->toBeTrue();
        expect($set->getWinner()->getName())->toBe($service->getName());
    });

    it('rest wins the set winning tie-break', function () {
        $service = createPlayer(1, 'Nadal');
        $rest = createPlayer(2, 'Federer');
        $set = createSet($service, $rest);

        for ($i = 0; $i < Set::MIN_GAMES_TO_WIN; $i++) {
            simulateSetGameWin($set, $rest);
            simulateSetGameWin($set, $service);
        }

        expect($set->isTieBreak())->toBeTrue();

        simulateTieBreakWin($set, $rest);

        expect($set->isFinished())->toBeTrue();
        expect($set->getWinner()->getName())->toBe($rest->getName());
    });
});
