<?php

use Tennis\Game;
use Tennis\Turn;

it('has a valid initial state on creation', function () {
    $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
    $game = createTennisGame($turn);
    expect($turn->getService()->getName())->toBe('Nadal');
    expect($turn->getRest()->getName())->toBe('Federer');
    expect($game->getScoreboard()->getPoints())->toBe([
        $turn->getService()->getId() => 0,
        $turn->getRest()->getId() => 0,
    ]);
});

it('increments the score of the server when scoring a point', function () {
    $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
    $game = createTennisGame($turn);
    $game->addPointTo($turn->getService());
    expect($game->getScoreboard()->getPoints())->toBe([
        $turn->getService()->getId() => 1,
        $turn->getRest()->getId() => 0,
    ]);
});

it('increments the score of the rest when scoring a point', function () {
    $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
    $game = createTennisGame($turn);
    $game->addPointTo($turn->getRest());
    expect($game->getScoreboard()->getPoints())->toBe([
        $turn->getService()->getId() => 0,
        $turn->getRest()->getId() => 1,
    ]);
});

describe('lack service', function () {
    it('does not change the score when the service has no faults', function () {
        $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
        $game = createTennisGame($turn);
        $game->lackService();
        expect($game->getScoreboard()->getPoints())->toBe([
            $turn->getService()->getId() => 0,
            $turn->getRest()->getId() => 0,
        ]);
        expect($game->getScoreboard()->isLackService())->toBeTrue();
    });

    it('increments the rest score when the service has two fault', function () {
        $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
        $game = createTennisGame($turn);
        $game->lackService();
        $game->lackService();
        expect($game->getScoreboard()->getPoints())->toBe([
            $turn->getService()->getId() => 0,
            $turn->getRest()->getId() => 1,
        ]);
    });

    it('fault service is reset after two faults', function () {
        $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
        $game = createTennisGame($turn);

        $game->lackService();
        $game->lackService();
        expect($game->getScoreboard()->isLackService())->toBeFalse();
    });

    it('fault service is reset after a point is scored', function () {
        $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
        $game = createTennisGame($turn);

        $game->lackService();
        $game->addPointTo($turn->getRest());

        expect($game->getScoreboard()->isLackService())->toBeFalse();
    });
});

it('service won the game', function () {
    $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
    $game = createTennisGame($turn);

    for ($i = 0; $i < 4; $i++) {
        $game->addPointTo($turn->getService());
    }

    expect($game->isWinner($turn->getService()))->toBeTrue();
});

it('rest won the game', function () {
    $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
    $game = createTennisGame($turn);

    for ($i = 0; $i < 4; $i++) {
        $game->addPointTo($turn->getRest());
    }

    expect($game->isWinner($turn->getRest()))->toBeTrue();
});

it('service won the game after deuce', function () {
    $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
    $game = createTennisGame($turn);

    for ($i = 0; $i < 3; $i++) {
        $game->addPointTo($turn->getService());
        $game->addPointTo($turn->getRest());
    }

    $game->addPointTo($turn->getService());
    $game->addPointTo($turn->getService());
    expect($game->isWinner($turn->getService()))->toBeTrue();
});

it('rest won the game after deuce', function () {
    $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
    $game = createTennisGame($turn);

    for ($i = 0; $i < 3; $i++) {
        $game->addPointTo($turn->getRest());
        $game->addPointTo($turn->getService());
    }

    $game->addPointTo($turn->getRest());
    $game->addPointTo($turn->getRest());
    expect($game->isWinner($turn->getRest()))->toBeTrue();
});

it('not win the game if the player has less than 4 points', function () {
    $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
    $game = createTennisGame($turn);

    for ($i = 1; $i < 4; $i++) {
        $game->addPointTo($turn->getService());
    }

    expect($game->isWinner($turn->getRest()))->toBeFalse();
});

it('assert error if trying to add point to a finished game', function () {
    $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
    $game = createTennisGame($turn);

    for ($i = 0; $i < 4; $i++) {
        $game->addPointTo($turn->getService());
    }

    expect(fn() => $game->addPointTo($turn->getService()))->toThrow(\AssertionError::class, 'Game is already finished.');
});

it('asdfasdfasf', function () {
    $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
    $game = createTennisGame($turn);

    for ($i = 0; $i < 3; $i++) {
        $game->addPointTo($turn->getService());
    }

    expect($game->isFinished())->toBeFalse();
    $game->lackService();
    $game->lackService();
    $game->addPointTo($turn->getService());
    expect($game->isFinished())->toBeTrue();
    expect($game->isWinner($turn->getService()))->toBeTrue();
});

it('rest with adventage won the game if service has 2 faults (lack service)', function () {
    $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
    $game = createTennisGame($turn);

    for ($i = 0; $i < Game::MIN_POINTS_TO_WIN; $i++) {
        $game->addPointTo($turn->getService());
        $game->addPointTo($turn->getRest());
    }

    expect($game->getScoreboard()->getPoints())->toBe([
        $turn->getService()->getId() => Game::MIN_POINTS_TO_WIN,
        $turn->getRest()->getId() => Game::MIN_POINTS_TO_WIN,
    ]);

    $game->addPointTo($turn->getRest()); // Rest has advantage
    expect($game->getScoreboard()->getPoints())->toBe([
        $turn->getService()->getId() => Game::MIN_POINTS_TO_WIN,
        $turn->getRest()->getId() => Game::MIN_POINTS_TO_WIN + 1,
    ]);

    $game->lackService(); // Service has two faults
    $game->lackService();
    expect($game->getScoreboard()->getPoints())->toBe([
        $turn->getService()->getId() => Game::MIN_POINTS_TO_WIN,
        $turn->getRest()->getId() => Game::MIN_POINTS_TO_WIN + Game::MIN_POINT_DIFFERENCE,
    ]);
});

describe('game ball', function () {
    it('is game ball when service has 3 points and rest has less than 3', function () {
        $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
        $game = createTennisGame($turn);
        for ($i = 0; $i < 3; $i++) {
            $game->addPointTo($turn->getService());
        }

        expect($game->isGameBall())->toBeTrue();
    });

    it('is game ball when rest has 3 points and service has less than 3', function () {
        $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
        $game = createTennisGame($turn);
        for ($i = 0; $i < 3; $i++) {
            $game->addPointTo($turn->getRest());
        }
        expect($game->isGameBall())->toBeTrue();
    });

    it('is game ball when service has 3 points and rest has adventage', function () {
        $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
        $game = createTennisGame($turn);
        for ($i = 0; $i < 3; $i++) {
            $game->addPointTo($turn->getService());
        }
        $game->addPointTo($turn->getRest()); // Rest has advantage
        expect($game->isGameBall())->toBeTrue();
    });

    it('is game ball when service has 8 points and rest has 6', function () {
        $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
        $game = createTennisGame($turn);
        for ($i = 0; $i < 6; $i++) {
            $game->addPointTo($turn->getService());
            $game->addPointTo($turn->getRest());
        }
        $game->addPointTo($turn->getService());

        expect($game->isGameBall())->toBeTrue();
    });

    it('is not game ball when both players have less than 3 points', function () {
        $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
        $game = createTennisGame($turn);
        expect($game->isGameBall())->toBeFalse();
    });

    it('is not game ball when both players have more than 3 points', function () {
        $turn = Turn::create([createPlayer('Nadal'), createPlayer('Federer')]);
        $game = createTennisGame($turn);
        for ($i = 0; $i < 4; $i++) {
            $game->addPointTo($turn->getService());
            $game->addPointTo($turn->getRest());
        }
        expect($game->isGameBall())->toBeFalse();
    });
});
