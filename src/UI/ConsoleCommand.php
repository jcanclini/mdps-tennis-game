<?php

declare(strict_types=1);

namespace Tennis\UI;

enum ConsoleCommand: string
{
    case CREATE_REFEREE = 'createReferee';
    case CREATE_PLAYER = 'createPlayer';
    case READ_PLAYERS = 'readPlayers';
    case CREATE_MATCH = 'createMatch';

    case LACK_SERVICE = 'lackService';
    case POINT_SERVICE = 'pointService';
    case POINT_REST = 'pointRest';
    case DISPLAY = 'display';

    case LOGIN = 'login';
    case LOGOUT = 'logout';

    case SIMULATION = 'simulation';

    case HELP = 'help';
    case EXIT = 'exit';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function hasCommand(string $command): bool
    {
        return in_array($command, self::values(), true);
    }
}
