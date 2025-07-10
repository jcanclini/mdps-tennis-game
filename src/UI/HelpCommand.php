<?php

namespace Tennis\UI;

class HelpCommand extends Command
{
    protected function execute(?string $args = null): void
    {
        $this->println("Available commands:");
        $this->println(">createReferee name:molina;password:1234");
        $this->println(">login name:molina;password:1234");
        $this->println(">createPlayer name:Nadal");
        $this->println(">createPlayer name:Alcaraz");
        $this->println(">createPlayer name:Zapata");
        $this->println(">readPlayers");
        $this->println(">createMatch sets:3;ids:1,2");
        $this->println(">lackService");
        $this->println(">pointService");
        $this->println(">pointRest");
        $this->println("-------------------------------------------");
    }
}
