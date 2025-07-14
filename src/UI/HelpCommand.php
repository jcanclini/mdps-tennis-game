<?php

namespace Tennis\UI;

class HelpCommand extends Command
{
    protected function execute(?string $args = null): void
    {
        $this->console->println("Available commands:");
        $this->console->println(">createReferee name:molina;password:1234");
        $this->console->println(">login name:molina;password:1234");
        $this->console->println(">createPlayer name:Nadal");
        $this->console->println(">createPlayer name:Alcaraz");
        $this->console->println(">createPlayer name:Zapata");
        $this->console->println(">readPlayers");
        $this->console->println(">createMatch sets:3;ids:1,2");
        $this->console->println(">lackService");
        $this->console->println(">pointService");
        $this->console->println(">pointRest");
        $this->console->println("-------------------------------------------");
    }
}
