<?php

declare(strict_types=1);

namespace Tennis;

class Scoreboard
{
    private TennisMatch $match;

    public function setMatch(TennisMatch $match): void
    {
        $this->match = $match;
    }

    public function draw(): void
    {
        if (empty($this->match)) {
            $this->println("No matches available.");
            return;
        }

        [$player1, $player2] = $this->match->getPlayers();

        if ($this->match->getCurrentGameService() === $player1) {
            $scorePlayer1 = $this->match->hasLackService() ? "+ " : "* ";
            $scorePlayer2 = "  ";
        } else {
            $scorePlayer1 = "  ";
            $scorePlayer2 = $this->match->hasLackService() ? "+ " : "* ";
        }

        [$score1, $score2] = $this->getScore($player1, $player2);

        $biggerName = max(strlen($player1->getName()), strlen($player2->getName()));

        $scorePlayer1 .= str_pad($player1->getName(), $biggerName, " ", STR_PAD_RIGHT) . ": {$score1}";
        $scorePlayer2 .= str_pad($player2->getName(), $biggerName, " ", STR_PAD_RIGHT) . ": {$score2}";

        foreach ($this->match->getSets() as $set) {
            $gamesWon = $set->getPoints();
            $player1Points = $gamesWon[0];
            $player2Points = $gamesWon[1];

            $scorePlayer1 .= $player1Points ? " {$player1Points}" : " -";
            $scorePlayer2 .= $player2Points ? " {$player2Points}" : " -";
        }

        for ($i = 0; $i < $this->match->getPendingSets(); $i++) {
            $scorePlayer1 .= " -";
            $scorePlayer2 .= " -";
        }

        $this->println($scorePlayer1);
        $this->println($scorePlayer2);

        if ($this->match->isGameBall()) {
            $this->println();
            $this->printBoxedMessage("Game Ball!!!");
            $this->println();
        }
        if ($this->match->isSetBall()) {
            $this->printBoxedMessage("Set Ball!!!");
            $this->println();
        }
        if ($this->match->isTieBreak()) {
            $this->println();
            $this->printBoxedMessage("Tie Break!!!");
            $this->println();
        }
    }

    public function getScore(Player $player1, Player $player2): array
    {
        if (!$this->match->isTieBreak()) {
            return [
                $this->getScoreForPlayer($player1),
                $this->getScoreForPlayer($player2)
            ];
        }

        if (
            $this->match->getPlayerPoints($player1) >= Game::MIN_POINTS_TO_WIN - 1 &&
            $this->match->getPlayerPoints($player2) >= Game::MIN_POINTS_TO_WIN - 1
        ) {
            $diff = $this->match->getPlayerPoints($player1) - $this->match->getPlayerPoints($player2);

            return match ($diff) {
                0 => ['40', '40'],
                1 => ['Ad', '40'],
                -1 => ['40', 'Ad']
            };
        }

        return [
            $this->getScoreForPlayer($player1),
            $this->getScoreForPlayer($player2)
        ];
    }

    public function getScoreForPlayer(Player $player): string
    {
        return match ($this->match->getPlayerPoints($player)) {
            0 => '0',
            1 => '15',
            2 => '30',
            3 => '40',
        };
    }

    protected function print(string $message): void
    {
        echo $message;
    }

    protected function println(string $message = ""): void
    {
        echo $message . PHP_EOL;
    }

    protected function printBoxedMessage(string $message): void
    {
        $length = strlen($message);
        $border = str_repeat('*', $length + 4);

        echo $border . PHP_EOL;
        echo "* $message *" . PHP_EOL;
        echo $border . PHP_EOL;
    }
}
