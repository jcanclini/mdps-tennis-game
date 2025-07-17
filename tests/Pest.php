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

function createTurn(?\Tennis\Player $server = null, ?\Tennis\Player $rest = null): \Tennis\Turn
{
    return \Tennis\Turn::create(
        $server ?? createPlayer('Nadal'),
        $rest ?? createPlayer('Federer')
    );
}

function createPlayer(string $name = 'Nadal'): \Tennis\Player
{
    return new \Tennis\Player(id: rand(1, 999), name: $name);
}

function createTennisGame(\Tennis\Turn $turn): \Tennis\Game
{
    $player1 = $player1 ?? createPlayer('Nadal');
    $player2 = $player2 ?? createPlayer('Federer');
    return new \Tennis\Game(
        1,
        $turn
    );
}

function createSet(\Tennis\Turn $turn): \Tennis\Set
{
    return new \Tennis\Set(
        1,
        $turn
    );
}

function createMatch(
    int $id = 1,
    ?\Tennis\Player $player1 = null,
    ?\Tennis\Player $player2 = null,
    int $setsToPlay = 3
): \Tennis\TennisMatch {
    return new \Tennis\TennisMatch(
        id: $id,
        player1: $player1 ?? createPlayer('Nadal'),
        player2: $player2 ?? createPlayer('Federer'),
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
        $set->addPointTo($player);
    }
}

function simulateSetGamesWon(\Tennis\TennisMatch $match, \Tennis\Player $player, int $games): void
{
    for ($i = 0; $i < $games; $i++) {
        simulateGamePointsWon($match, $player, \Tennis\Game::MIN_POINTS_TO_WIN);
    }
}

function simulateGamePointsWon(\Tennis\TennisMatch $match, \Tennis\Player $player, int $points): void
{
    assert($points >= 0, 'Points must be a non-negative integer.');
    assert($points <= \Tennis\Game::MIN_POINTS_TO_WIN, 'Points must be less than or equal to ' . \Tennis\Game::MIN_POINTS_TO_WIN);

    for ($i = 0; $i < $points; $i++) {
        $match->addPointTo($player);
    }
}
