<?php

use Tennis\Turn;

describe('Turn', function () {
    it('has a valid initial state on creation', function () {
        $player1 = createPlayer('Nadal');
        $player2 = createPlayer('Federer');
        $turn = Turn::create([$player1, $player2]);

        expect($turn->getService())->toBe($player1);
        expect($turn->getRest())->toBe($player2);
    });

    it('can get the opponent of a player', function () {
        $player1 = createPlayer('Nadal');
        $player2 = createPlayer('Federer');
        $turn = Turn::create([$player1, $player2]);

        expect($turn->getOpponent($player1))->toBe($player2);
        expect($turn->getOpponent($player2))->toBe($player1);
    });

    it('can switch players', function () {
        $player1 = createPlayer('Nadal');
        $player2 = createPlayer('Federer');
        $turn = Turn::create([$player1, $player2]);

        expect($turn->getService())->toBe($player1);
        expect($turn->getRest())->toBe($player2);

        $turn->switch();
        $turn->switch();

        expect($turn->getService())->toBe($player2);
        expect($turn->getRest())->toBe($player1);
    });
});

