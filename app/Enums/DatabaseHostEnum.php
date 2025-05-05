<?php

namespace App\Enums;

class DatabaseHostEnum
{
    const DEV = '192.168.68.1';
    const STAGING = '192.168.69.1';
    const LIVE = '192.168.70.2';

    /**
    * Get all database hosts.
    *
    * @return array
    */
    public static function all(): array
    {
        return [
            self::DEV,
            self::STAGING,
            self::LIVE
        ];
    }
}
