<?php

use Tennis\Player;
use Tennis\TieBreak;

function createTieBreak(Player $service, Player $rest): TieBreak
{
    return new TieBreak(
        1,
        $service,
        $rest
    );
}

it('has a valid initial state on creation', function () {
    $service = new Player(id: 1, name: 'Nadal');
    $rest = new Player(id: 2, name: 'Federer');
    $tieBreak = createTieBreak($service, $rest);

    expect($tieBreak->getService()->getName())->toBe('Nadal');
    expect($tieBreak->getRest()->getName())->toBe('Federer');
});

it('serves the first point', function () {
    $service = new Player(id: 1, name: 'Nadal');
    $rest = new Player(id: 2, name: 'Federer');
    $tieBreak = createTieBreak($service, $rest);

    $tieBreak->addPointToService();
    expect($tieBreak->getPoints($service))->toBe(1);
    expect($tieBreak->getPoints($rest))->toBe(0);

});

it('service wins the game', function () {
    $service = new Player(id: 1, name: 'Nadal');
    $rest = new Player(id: 2, name: 'Federer');
    $tieBreak = createTieBreak($service, $rest);

    $tieBreak->addPointToService();
    expect($tieBreak->getPoints($service))->toBe(1);
    $tieBreak->addPointToRest();
    expect($tieBreak->getPoints($service))->toBe(2);
    $tieBreak->addPointToRest();
    expect($tieBreak->getPoints($service))->toBe(3);
    $tieBreak->addPointToService();
    expect($tieBreak->getPoints($service))->toBe(4);
    $tieBreak->addPointToService();
    expect($tieBreak->getPoints($service))->toBe(5);
    $tieBreak->addPointToRest();
    expect($tieBreak->getPoints($service))->toBe(6);

    expect($tieBreak->getWinner()->getName())->toBe('Nadal');
});

it('switches service after odd points', function () {
    $service = new Player(id: 1, name: 'Nadal');
    $rest = new Player(id: 2, name: 'Federer');
    $tieBreak = createTieBreak($service, $rest);

    expect($tieBreak->getService()->getName())->toBe('Nadal');

    $tieBreak->addPointToService();
    expect($tieBreak->getService()->getName())->toBe('Federer');

    $tieBreak->addPointToRest();
    $tieBreak->addPointToService();
    expect($tieBreak->getService()->getName())->toBe('Nadal');

    $tieBreak->addPointToService();
    $tieBreak->addPointToRest();
    expect($tieBreak->getService()->getName())->toBe('Federer');
});

it('is game ball when service has 6 points and rest has 4 or less', function () {
    $service = new Player(id: 1, name: 'Nadal');
    $rest = new Player(id: 2, name: 'Federer');
    $tieBreak = createTieBreak($service, $rest);

    for ($i = 0; $i < 5; $i++) {
        $tieBreak->addPointToService();
    }
    for ($i = 0; $i < 4; $i++) {
        $tieBreak->addPointToRest();
    }

    expect($tieBreak->isGameBall())->toBeTrue();
});