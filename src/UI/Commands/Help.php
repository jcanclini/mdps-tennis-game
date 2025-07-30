<?php

declare(strict_types=1);

namespace Tennis\UI\Commands;

use Tennis\UI\Command;

class Help extends Command
{
    public function run(?string $args = null): void
    {
        $this->viewIO->writeLine(
            "Available commands:" . PHP_EOL .
            "  -------------------------------------------" . PHP_EOL .
            "  Guest:" . PHP_EOL .
            "    >createReferee name:molina;password:1234" . PHP_EOL .
            "    >login name:molina;password:1234" . PHP_EOL .
            "  -------------------------------------------" . PHP_EOL .
            "  Authenticated:" . PHP_EOL .
            "    >createPlayer name:Nadal" . PHP_EOL .
            "    >createPlayer name:Alcaraz" . PHP_EOL .
            "    >createPlayer name:Zapata" . PHP_EOL .
            "    >readPlayers" . PHP_EOL .
            "  --------------------------------------------" . PHP_EOL .
            "  Match:" . PHP_EOL .
            "    >createMatch sets:3;ids:1,2" . PHP_EOL .
            "    >lackService" . PHP_EOL .
            "    >pointService" . PHP_EOL .
            "    >pointRest" . PHP_EOL
        );
    }
}
