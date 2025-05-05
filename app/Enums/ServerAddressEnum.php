<?php

namespace App\Enums;

class ServerAddressEnum
{
    const DEV = '192.168.190.107';
    const STAGING = '192.168.189.107';
    const LIVE = '192.168.188.107';

    /**
    * Get all server addresses.
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
