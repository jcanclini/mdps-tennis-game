<?php

namespace Tennis\UI\Commands;

use Tennis\TennisController;
use Tennis\UI\ViewIO;

class SimulationIO extends ViewIO
{
    private array $commands = [
        'createReferee name:molina;password:1234',
        'login name:molina;password:1234',
        'createPlayer name:Nadal',
        'createPlayer name:Federer',
        'createPlayer name:Thiem',
        'readPlayers',
        'createMatch sets:3;ids:1,2'
    ];

    public function __construct(private TennisController $tennisController) {}

    public function read(string $prompt = '>'): string
    {
        if ($this->tennisController->getCurrentMatch() && $this->tennisController->getScoreboard()->isMatchFinished()) {
            exit(0);
        }

        sleep(1);

        if (empty($this->commands)) {
            $options = ['pointService', 'pointRest', 'lackService'];
            $command = $options[array_rand($options)];
            $this->writeLine($prompt . $command);
            return $command;
        }

        $command = $this->commands[0];
        array_shift($this->commands);
        $this->writeLine($prompt . $command);
        return $command;
    }
}
