<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/


function createPlayer(int $id = 1, string $name = 'Nadal'): \Tennis\Player
{
    return new \Tennis\Player(id: $id, name: $name);
}

function createTennisGame(?\Tennis\Player $player1 = null, ?\Tennis\Player $player2 = null): \Tennis\Game
{
    $player1 = $player1 ?? createPlayer(1, 'Nadal');
    $player2 = $player2 ?? createPlayer(2, 'Federer');
    return \Tennis\Game::create(
        1,
        \Tennis\Turn::create(
            $player1,
            $player2
        )
    );
}

function createSet(?\Tennis\Player $player1 = null, ?\Tennis\Player $player2 = null): \Tennis\Set
{
    return \Tennis\Set::create(
        1,
        service: $player1 ?? createPlayer(1, 'Nadal'),
        rest: $player2 ?? createPlayer(2, 'Federer')
    );
}

function createMatch(
    int $id = 1,
    ?\Tennis\Player $player1 = null,
    ?\Tennis\Player $player2 = null,
    int $setsToPlay = 3
): \Tennis\TennisMatch {
    return \Tennis\TennisMatch::create(
        id: $id,
        player1: $player1 ?? createPlayer(1, 'Nadal'),
        player2: $player2 ?? createPlayer(2, 'Federer'),
        setsToPlay: $setsToPlay
    );
}

function simulateSetWin(\Tennis\Set $set, \Tennis\Player $player): void
{
    for ($i = 0; $i < \Tennis\Set::MIN_GAMES_TO_WIN; $i++) {
        simulateSetGameWin($set, $player);
    }
}

function simulateSetGameWin(\Tennis\Set $set, \Tennis\Player $player): void
{
    for ($i = 0; $i < \Tennis\Game::MIN_POINTS_TO_WIN; $i++) {
        if ($set->getCurrentGame()->getService() === $player) {
            $set->addPointToService();
        } else {
            $set->addPointToRest();
        }
    }
}
