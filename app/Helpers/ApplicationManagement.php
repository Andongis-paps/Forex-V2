<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApplicationManagement {
    public static function getApplicationURLBaseOnServer($URL) {
        $server_ip = getServerIpAddress();

        if ($server_ip === '192.168.190.107') {
            $new_application_url = 'http://'.$URL.'.dev.sinag'; // Dev Server
        } else if ($server_ip === '192.168.189.107') {
            $new_application_url = 'http://'.$URL.'.stg.sinag'; // Staging Server
        } else if ($server_ip === '192.168.188.107') {
            $new_application_url = 'http://'.$URL.'.sinag'; // Live Server
        } else {
            $new_application_url = 'http://'.$URL.'.dev.sinag'; // Default to Dev Server
        }

        return $new_application_url;
    }
}
