<?php

declare(strict_types=1);

namespace Tennis\UI\Views;

use Tennis\Game;
use Tennis\Player;
use Tennis\Scoreboard as ScoreboardDTO;
use Tennis\TennisController;
use Tennis\UI\View;

class Scoreboard extends View
{
    private const SCORE_MAP = [
        0 => '0',
        1 => '15',
        2 => '30',
        3 => '40',
    ];

    private array $players;

    public function __construct(TennisController $tennisController)
    {
        parent::__construct($tennisController, []);
        $this->players = $tennisController->getScoreboard()->getPlayers();
    }

    public function render(): void
    {
        $score = $this->getScore($this->tennisController->getScoreboard());

        foreach ($this->players as $player) {
            $this->viewIO->writeLine($this->formatScoreLine($this->tennisController->getScoreboard(), $player, $score));
        }

        if ($this->tennisController->getScoreboard()->isMatchFinished()) {
            $this->viewIO->writeLine("Match finished!");
            return;
        }

        if ($this->tennisController->getScoreboard()->isGameBall()) {
            $this->viewIO->writeLine();
            $this->printBoxedMessage("Game Ball!!!");
        }
        if ($this->tennisController->getScoreboard()->isSetBall()) {
            $this->printBoxedMessage("Set Ball!!!");
        }
        if ($this->tennisController->getScoreboard()->isTieBreak()) {
            $this->printBoxedMessage("Tie Break!!!");
        }
        if ($this->tennisController->getScoreboard()->isMatchBall()) {
            $this->printBoxedMessage("Match Ball!!!");
        }
    }

    private function formatScoreLine(ScoreboardDTO $scoreboard, Player $player, array $score): string
    {
        $line = "  ";
        if ($scoreboard->getService()->is($player)) {
            $line = $scoreboard->isLackService() ? "+ " : "* ";
        }

        $line .= str_pad($player->getName(), $this->getLongestPlayerNameLength($scoreboard->getPlayers()), " ", STR_PAD_RIGHT);
        $line .= ": " . str_pad($score[$player->getId()], $this->getLongestStringLength($score), " ", STR_PAD_RIGHT);

        foreach ($scoreboard->getSets() as $set) {
            $line .= $set->getGamesWonBy($player) ? " {$set->getGamesWonBy($player)}" : " 0";
        }

        $line .= str_repeat(" -", $scoreboard->getPendingSets());

        return $line;
    }

    private function getLongestPlayerNameLength(array $players): int
    {
        return $this->getLongestStringLength(array_map(fn(Player $p) => $p->getName(), $players));
    }

    private function getLongestStringLength(array $strings): int
    {
        return max(array_map(fn($string) => strlen($string), $strings));
    }

    private function getScore(ScoreboardDTO $scoreboard): array
    {
        if ($scoreboard->isMatchFinished()) {
            return array_fill_keys(array_keys($scoreboard->getPoints()), '0');
        }

        [$p1Id, $p2Id] = array_map(fn(Player $p) => $p->getId(), $this->players);

        if ($scoreboard->isTieBreak()) {
            return array_map(fn($point) => (string)$point, $scoreboard->getPoints());
        }

        if ($this->bothPlayersHasMinPointToWin($scoreboard)) {
            return match ($scoreboard->getPointsDifference()) {
                0 => array_fill_keys([$p1Id, $p2Id], '40'),
                1 => $this->getAdvantageScore($p1Id, $p2Id),
                -1 => $this->getAdvantageScore($p2Id, $p1Id)
            };
        }

        return [
            $p1Id => self::SCORE_MAP[$scoreboard->getPoints()[$p1Id]],
            $p2Id => self::SCORE_MAP[$scoreboard->getPoints()[$p2Id]]
        ];
    }

    private function bothPlayersHasMinPointToWin(ScoreboardDTO $scoreboard): bool
    {
        return count(array_filter($scoreboard->getPoints(), fn($points) => $points >= Game::MIN_POINTS_TO_WIN - 1)) === 2;
    }

    private function getAdvantageScore(int $p1Id, int $p2Id): array
    {
        return [$p1Id => 'Ad', $p2Id => '40'];
    }
}
