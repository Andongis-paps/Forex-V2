<?php

namespace App\Helpers;
use Illuminate\Support\Facades\DB;

class AutorunNotification {
    public static function autorun() {
        // Set a reasonable time limit for the PHP script
        // set_time_limit(10);

        // Check if the Node.js server is already running and kill it if necessary
        $killCommand = 'taskkill /F /IM node.exe > NUL 2>&1';
        exec($killCommand);

        // Start the Node.js server in the background
        // $command = 'start /B node C:/laragon/www/Forex/socket-server/server.js > NUL 2>&1';
        // $command = 'start /B node \\192.168.189.107\htdocs\Forex\socket-server\server.js > NUL 2>&1';
        $command = 'start /B node Y:\Forex\socket-server\server.js > output.log 2>&1';
        exec($command);

        // Delay to give the server time to start (adjust time as needed)
        // sleep(5);

        // Optionally, check if the server is running
        $serverUrl = 'http://localhost:3434';
        $response = @file_get_contents($serverUrl);

        if ($response === FALSE) {
            dd("Failed to connect to the server. It might not be running.");
        } else {
            dd("Node.js server is running.");
        }

        // Stop the Node.js server (optional, based on your requirements)
        $killCommand = 'taskkill /F /IM node.exe > NUL 2>&1';
        exec($killCommand);
        //    dd("Node.js server stopped.");
    }
}
