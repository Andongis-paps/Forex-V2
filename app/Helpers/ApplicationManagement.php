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

    public static function current() {
        $config = DB::connection('access')
                    ->table('tblsystemconfig')
                    ->where('SoftwareID', config('app.software_id'))
                    ->latest('DateCreated')
                    ->first();
 
        return $config;
    }
 
    public static function copyright() {
          $copyright = '&copy; ' . date('Y') . ' Sinag Pawnshop Corp. All Rights Reserved.';
     
          $config = self::current();
     
          if ($config) {
               $copyright = $config->Copyright;
          }
     
          return $copyright;
    }
 
    public static function version() {
          $version = '2.0.0';
     
          $config = self::current();
     
          if ($config) {
               $version = $config->Version;
          }
     
          return $version;
    }
}
