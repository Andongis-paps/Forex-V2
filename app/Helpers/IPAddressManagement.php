<?php

    if (!function_exists('getServerIpAddress')) {
        function getServerIpAddress() {
            return $_SERVER['SERVER_ADDR'] ?? '127.0.0.1';
        }
    }

    // if (!function_exists('getServerIpAddress')) {
    //     function getServerIpAddress() {
    //         // Try to get the server IP from SERVER_ADDR
    //         $serverIp = $_SERVER['SERVER_ADDR'] ?? null;

    //         // If SERVER_ADDR is not available, try LOCAL_ADDR or gethostbyname()
    //         if (!$serverIp) {
    //             $serverIp = $_SERVER['LOCAL_ADDR'] ?? gethostbyname(gethostname());
    //         }

    //         return $serverIp;
    //     }
    // }

