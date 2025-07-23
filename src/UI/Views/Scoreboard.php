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

        $line .= str_pad($player->getName(), $this->getLongestPlayerNameLength($scoreboard), " ", STR_PAD_RIGHT) . ": {$score[$player->getId()]}";

        foreach ($scoreboard->getSets() as $set) {
            $line .= $set->getGamesWonBy($player) ? " {$set->getGamesWonBy($player)}" : " -";
        }

        $line .= str_repeat(" -", $scoreboard->getPendingSets());

        return $line;
    }

    private function getLongestPlayerNameLength(ScoreboardDTO $scoreboard): int
    {
        return max(array_map(fn(Player $p) => strlen($p->getName()), $scoreboard->getPlayers()));
    }

    private function getScore(ScoreboardDTO $scoreboard): array
    {
        [$p1Id, $p2Id] = array_map(fn(Player $p) => $p->getId(), $this->players);

        if ($scoreboard->isTieBreak()) {
            return $scoreboard->getPoints();
        }

        if ($this->bothPlayersHasMinPointToWin($scoreboard)) {
            return match ($scoreboard->getPointsDifference()) {
                0 => array_fill_keys([$p1Id, $p2Id], 'Deuce'),
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
