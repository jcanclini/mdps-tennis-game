<?php

use Tennis\Player;
use Tennis\Set;
use Tennis\TieBreak;
use Tennis\Turn;

function simulateTieBreakWin(Set $set, Player $player): void
{
    for ($i = 0; $i < TieBreak::MIN_POINTS_TO_WIN; $i++) {
        $set->addPointTo($player);
    }
}

it('has a valid initial state on creation', function () {
    $set = createSet(Turn::create([createPlayer('Nadal'), createPlayer('Federer')]));
    expect($set->getId())->toBe(1);
    expect($set->isFinished())->toBeFalse();
});

it('service won the first game', function () {
    $service = createPlayer();
    $turn = Turn::create([$service, createPlayer('Federer')]);
    $set = createSet($turn);

    simulateSetGameWin($set, $service);

    expect($set->getGamesWonBy($service))->toBe(1);
});

it('rest won the set', function () {
    $service = createPlayer('Nadal');
    $rest = createPlayer('Federer');
    $turn = Turn::create([$service, $rest]);
    $set = createSet($turn);

    simulateSetWin($set, $rest);

    expect($set->isWinner($rest))->toBeTrue();
});

it('player 1 and player 2 are tied 1-1 before server won 2 games of the set', function () {
    $service = createPlayer('Nadal');
    $rest = createPlayer('Federer');
    $turn = Turn::create([$service, $rest]);
    $set = createSet($turn);

    simulateSetGameWin($set, $service);
    simulateSetGameWin($set, $rest);

    expect($set->getGamesWonBy($service))->toBe(1);
    expect($set->getGamesWonBy($rest))->toBe(1);
});

describe('is set ball', function () {
    it('returns true when the service has 5 games won and 3 point in current game and the rest hass less than 5 sets won', function () {
        $service = createPlayer('Nadal');
        $turn = Turn::create([$service, createPlayer('Federer')]);
        $set = createSet($turn);

        for ($i = 0; $i < 5; $i++) {
            simulateSetGameWin($set, $service);
        }

        $set->addPointTo($service);
        $set->addPointTo($service);
        $set->addPointTo($service);
        expect($set->getScoreboard()->isSetBall())->toBeTrue();
    });

    it('returns false when the service has less than 5 sets won', function () {
        $service = createPlayer('Nadal');
        $turn = Turn::create([$service, createPlayer('Federer')]);
        $set = createSet($turn);

        for ($i = 0; $i < 4; $i++) {
            simulateSetGameWin($set, $service);
        }

        expect($set->getScoreboard()->isSetBall())->toBeFalse();
    });

    it('returns false when the service has 5 sets won but less than 3 points in current game', function () {
        $service = createPlayer('Nadal');
        $turn = Turn::create([$service, createPlayer('Federer')]);
        $set = createSet($turn);

        for ($i = 0; $i < 4; $i++) {
            simulateSetGameWin($set, $service);
        }

        $set->addPointTo($service);
        $set->addPointTo($service);
        $set->addPointTo($service);
        expect($set->getScoreboard()->isSetBall())->toBeFalse();
    });
});

describe('tie break', function () {
    it('is created when the set is tied at 6-6', function () {
        $service = createPlayer('Nadal');
        $rest = createPlayer('Federer');
        $turn = Turn::create([$service, $rest]);
        $set = createSet($turn);


        for ($i = 0; $i < Set::MIN_GAMES_TO_WIN; $i++) {
            simulateSetGameWin($set, $service);
            simulateSetGameWin($set, $rest);
        }

        expect($set->getScoreboard()->isTieBreak())->toBeTrue();
    });

    it('service wins the set winning tie-break', function () {
        $service = createPlayer('Nadal');
        $rest = createPlayer('Federer');
        $set = createSet(Turn::create([$service, $rest]));

        for ($i = 0; $i < Set::MIN_GAMES_TO_WIN; $i++) {
            simulateSetGameWin($set, $service);
            simulateSetGameWin($set, $rest);
        }

        expect($set->getScoreboard()->isTieBreak())->toBeTrue();

        simulateTieBreakWin($set, $service);

        expect($set->isFinished())->toBeTrue();
        expect($set->isWinner($service))->toBeTrue();
    });

    it('rest wins the set winning tie-break', function () {
        $service = createPlayer('Nadal');
        $rest = createPlayer('Federer');
        $set = createSet(Turn::create([$service, $rest]));

        for ($i = 0; $i < Set::MIN_GAMES_TO_WIN; $i++) {
            simulateSetGameWin($set, $rest);
            simulateSetGameWin($set, $service);
        }

        expect($set->getScoreboard()->isTieBreak())->toBeTrue();

        simulateTieBreakWin($set, $rest);

        expect($set->isFinished())->toBeTrue();
        expect($set->isWinner($rest))->toBeTrue();
    });
});
