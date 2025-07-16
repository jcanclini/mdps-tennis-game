<?php

namespace Tennis\UI;

class SimulationCommand extends Command
{
    protected function execute(?string $args = null): void
    {
        $this->console->println("Starting simulation...");
        $this->console->println(">createReferee name:molina;password:1234");
        new CreateRefereeCommand($this->console)('name:molina;password:1234');
        sleep(1);
        $this->console->println(">login name:molina;password:1234");
        new LoginCommand($this->console)('name:molina;password:1234');
        sleep(1);
        $this->console->println(">createPlayer name:Nadal");
        new CreatePlayerCommand($this->console)('name:Nadal');
        sleep(1);
        $this->console->println(">createPlayer name:Alcaraz");
        new CreatePlayerCommand($this->console)('name:Alcaraz');
        sleep(1);
        $this->console->println(">readPlayers");
        new ReadPlayersCommand($this->console)();
        sleep(1);
        $this->console->println(">createMatch sets:3;ids:1,2");
        new CreateMatchCommand($this->console)('sets:3;ids:1,2');
        sleep(1);
        $this->console->println("id: {$this->console->getGame()->getMatchId()}");
        $this->console->println("date: {$this->console->getGame()->getMatchDate()}");
        $this->console->println("Referee: {$this->console->getGame()->getRefereeName()}");
        $this->console->println();

        $commands = [
            PointServiceCommand::class,
            PointRestCommand::class,
        ];
        $commandsNames = [
            PointServiceCommand::class => 'pointService',
            PointRestCommand::class => 'pointRest',
        ];
        $this->console->println("Simulating player 1 winning 4 games in a row:");
        for ($i = 0; $i < 12; $i++) {
            for ($j = 0; $j < 4; $j++) {
                $command = $commands[$i % 2];
                $this->console->println("match id:1>{$commandsNames[$command]}");
                (new $command($this->console))();
                $this->console->println();
                sleep(1); // Simulate some delay for better visibility
                
            }
        }
    }
}
