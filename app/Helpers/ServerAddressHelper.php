<?php

namespace App\Helpers;

use App\Enums\DatabaseHostEnum;
use App\Enums\ServerAddressEnum;

class ServerAddressHelper
{
    /**
     * Get the current server address.
     *
     * @return string
     */
    public static function getCurrentServerAddress(): string
    {
        return $_SERVER['SERVER_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Get the corresponding database host based on the current server address.
     *
     * @return string
     */
    public static function getDbHost(): string
    {
        // Get the current server address
        $serverAddress = self::getCurrentServerAddress();

        // Map the server addresses to their corresponding database hosts
        $dbHosts = [
            ServerAddressEnum::DEV      => DatabaseHostEnum::DEV,
            ServerAddressEnum::STAGING  => DatabaseHostEnum::STAGING,
            ServerAddressEnum::LIVE     => DatabaseHostEnum::LIVE
        ];

        // Return the corresponding DB host or default to Dev if not found
        return $dbHosts[$serverAddress] ?? DatabaseHostEnum::DEV;
    }

    /**
     * Get the corresponding app URL based on the current server address.
     *
     * @param string|null $appUrlName
     * @return string
     */
    public static function getAppUrl(string $appUrlName = null): string
    {
        // Use the provided appUrlName or get the default from the config
        $appUrlName = $appUrlName ?? config('main.app_url_name');

        // Get the current server address
        $serverAddress = self::getCurrentServerAddress();

        // Map the server addresses to their corresponding app URLs
        $appUrls = [
            ServerAddressEnum::DEV     => "http://{$appUrlName}.dev.sinag",
            ServerAddressEnum::STAGING => "http://{$appUrlName}.stg.sinag",
            ServerAddressEnum::LIVE    => "http://{$appUrlName}.sinag"
        ];

        // Determine the app URL
        if (array_key_exists($serverAddress, $appUrls)) {
            $url = $appUrls[$serverAddress];
        } else {
            // Fallback to local development URL or default DEV URL
            $url = $appUrlName == config('main.app_url_name')
                ? "http://{$appUrlName}.test"
                : $appUrls[ServerAddressEnum::DEV];
        }

        // Return the app URL
        return $url;
    }
}
