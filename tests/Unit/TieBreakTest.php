<?php

use Tennis\Player;
use Tennis\TieBreak;
use Tennis\Turn;

function createTieBreak(Turn $turn): TieBreak
{
    return new TieBreak(1, $turn);
}

function simulateTieBreakWinner(TieBreak $tieBreak, Player $winner): void
{
    for ($i = 0; $i < TieBreak::MIN_POINTS_TO_WIN; $i++) {
        $tieBreak->addPointTo($winner);
    }
}

describe('TieBreak', function () {

    it('has a valid initial state on creation', function () {
        $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
        createTieBreak($turn);

        expect($turn->getService()->getName())->toBe('Nadal');
        expect($turn->getRest()->getName())->toBe('Federer');
    });

    it('serves the first point', function () {
        $player1 = createPlayer('Nadal');
        $player2 = createPlayer('Federer');
        $turn = Turn::create([$player1, $player2]);
        $tieBreak = createTieBreak($turn);

        $tieBreak->addPointTo($turn->getService());
        expect($tieBreak->getScoreboard()->getPoints())->toBe([
            $player1->getId() => 1,
            $player2->getId() => 0,
        ]);
    });

    it('service wins the game', function () {
        $service = createPlayer('Nadal');
        $turn = Turn::create([$service, createPlayer('Federer')]);
        $tieBreak = createTieBreak($turn);

        simulateTieBreakWinner($tieBreak, $service);

        expect($tieBreak->isWinner($service))->toBeTrue();
    });

    it('rest wins the game', function () {
        $rest = createPlayer('Federer');
        $turn = Turn::create([createPlayer('Nadal'), $rest]);
        $tieBreak = createTieBreak($turn);

        simulateTieBreakWinner($tieBreak, $rest);

        expect($tieBreak->isWinner($rest))->toBeTrue();
    });

    it('switches service after odd points', function () {
        $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
        $tieBreak = createTieBreak($turn);

        expect($turn->getService()->getName())->toBe('Nadal');

        $tieBreak->addPointTo($turn->getService());
        expect($turn->getService()->getName())->toBe('Federer');

        $tieBreak->addPointTo($turn->getService());
        $tieBreak->addPointTo($turn->getService());
        expect($turn->getService()->getName())->toBe('Nadal');

        $tieBreak->addPointTo($turn->getService());
        $tieBreak->addPointTo($turn->getService());
        expect($turn->getService()->getName())->toBe('Federer');
    });

    it('is game ball when service has 6 points and rest has 4 or less', function () {
        $service = createPlayer('Nadal');
        $rest = createPlayer('Federer');
        $turn = Turn::create([$service, $rest]);
        $tieBreak = createTieBreak($turn);

        for ($i = 0; $i < 5; $i++) {
            $tieBreak->addPointTo($service);
        }
        for ($i = 0; $i < 4; $i++) {
            $tieBreak->addPointTo($rest);
        }

        expect($tieBreak->isGameBall())->toBeTrue();
    });
});
