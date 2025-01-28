<?php

namespace App\Enums;

enum Status : string {

    case OFFLINE = 'Offline';

    case ONLINE = 'Online';

    case INVISIBLE = 'Invisible';

    public static function getFromTime(float $lastHeartBeat) : Status
    {
        $now = microtime(true);
        $now -= $lastHeartBeat;
        if ($now > 0.9)
        {
            return self::OFFLINE;
        }
        return self::ONLINE;
    }
    
}