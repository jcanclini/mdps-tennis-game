<?php

it('has a valid initial state on creation', function () {
    $game = createTennisGame();
    expect($game->getService()->getName())->toBe('Nadal');
    expect($game->getRest()->getName())->toBe('Federer');
    expect($game->getPoints($game->getService()))->toBe(0);
    expect($game->getPoints($game->getRest()))->toBe(0);
});

it('increments the score of the server when scoring a point', function () {
    $game = createTennisGame();
    $game->addPointToService();
    expect($game->getPoints($game->getService()))->toBe(1);
    expect($game->getPoints($game->getRest()))->toBe(0);
});

it('increments the score of the rest when scoring a point', function () {
    $game = createTennisGame();
    $game->addPointToRest();
    expect($game->getPoints($game->getService()))->toBe(0);
    expect($game->getPoints($game->getRest()))->toBe(1);
});

describe('lack service', function () {
    it('does not change the score when the service has no faults', function () {
        $game = createTennisGame();
        $game->lackService();
        expect($game->getPoints($game->getService()))->toBe(0);
        expect($game->getPoints($game->getRest()))->toBe(0);
        expect($game->isLackService())->toBeTrue();
    });

    it('increments the rest score when the service has two fault', function () {
        $game = createTennisGame();
        $game->lackService();
        $game->lackService();
        expect($game->getPoints($game->getService()))->toBe(0);
        expect($game->getPoints($game->getRest()))->toBe(1);
    });

    it('fault service is reset after two faults', function () {
        $game = createTennisGame();

        $game->lackService();
        $game->lackService();
        expect($game->isLackService())->toBeFalse();
    });

    it('fault service is reset after a point is scored', function () {
        $game = createTennisGame();

        $game->lackService();
        $game->addPointToRest();

        expect($game->isLackService())->toBeFalse();
    });
});

it('service won the game', function () {
    $game = createTennisGame();

    for ($i = 0; $i < 4; $i++) {
        $game->addPointToService();
    }

    expect($game->getWinner())->toBe($game->getService());
});

it('rest won the game', function () {
    $game = createTennisGame();

    for ($i = 0; $i < 4; $i++) {
        $game->addPointToRest();
    }

    expect($game->getWinner())->toBe($game->getRest());
});

it('service won the game after deuce', function () {
    $game = createTennisGame();

    for ($i = 0; $i < 3; $i++) {
        $game->addPointToService();
        $game->addPointToRest();
    }

    $game->addPointToService();
    $game->addPointToService();
    expect($game->getWinner())->toBe($game->getService());
});

it('rest won the game after deuce', function () {
    $game = createTennisGame();

    for ($i = 0; $i < 3; $i++) {
        $game->addPointToRest();
        $game->addPointToService();
    }

    $game->addPointToRest();
    $game->addPointToRest();
    expect($game->getWinner())->toBe($game->getRest());
});

it('not win the game if the player has less than 4 points', function () {
    $game = createTennisGame();

    for ($i = 1; $i < 4; $i++) {
        $game->addPointToService();
    }

    expect($game->getWinner())->toBeNull(); //(\AssertionError::class, 'Game is not finished yet.');
});

it('assert error if trying to add point to a finished game', function () {
    $game = createTennisGame();

    for ($i = 0; $i < 4; $i++) {
        $game->addPointToService();
    }

    expect(fn() => $game->addPointToService())->toThrow(\AssertionError::class, 'Game is already finished.');
});

it('asdfasdfasf', function () {
    $game = createTennisGame();

    for ($i = 0; $i < 3; $i++) {
        $game->addPointToService();
    }

    expect($game->isFinished())->toBeFalse();
    $game->lackService();
    $game->lackService();
    $game->addPointToService();
    expect($game->isFinished())->toBeTrue();
    expect($game->getWinner())->toBe($game->getService());
});

describe('game ball', function () {
    it('is game ball when service has 3 points and rest has less than 3', function () {
        $game = createTennisGame();
        for ($i = 0; $i < 3; $i++) {
            $game->addPointToService();
        }

        expect($game->isGameBall())->toBeTrue();
    });

    it('is game ball when rest has 3 points and service has less than 3', function () {
        $game = createTennisGame();
        for ($i = 0; $i < 3; $i++) {
            $game->addPointToRest();
        }
        expect($game->isGameBall())->toBeTrue();
    });

    it('is game ball when service has 3 points and rest has adventage', function () {
        $game = createTennisGame();
        for ($i = 0; $i < 3; $i++) {
            $game->addPointToService();
        }
        $game->addPointToRest(); // Rest has advantage
        expect($game->isGameBall())->toBeTrue();
    });

    it('is game ball when service has 8 points and rest has 6', function () {
        $game = createTennisGame();
        for ($i = 0; $i < 6; $i++) {
            $game->addPointToService();
            $game->addPointToRest();
        }
        $game->addPointToService();

        expect($game->isGameBall())->toBeTrue();
    });

    it('is not game ball when both players have less than 3 points', function () {
        $game = createTennisGame();
        expect($game->isGameBall())->toBeFalse();
    });

    it('is not game ball when both players have more than 3 points', function () {
        $game = createTennisGame();
        for ($i = 0; $i < 4; $i++) {
            $game->addPointToService();
            $game->addPointToRest();
        }
        expect($game->isGameBall())->toBeFalse();
    });
});
