<?php

namespace App\Helpers;
use App\Helpers\ApplicationManagement;
use Illuminate\Support\Facades\Http;

class UserManagement {
    public static function getProfilePicture($UserProfile) {
        // Get the ALMS base URL
        $almsBaseUrl = ApplicationManagement::getApplicationURLBaseOnServer('uams');

        // Generate the full image URL
        $imageLink = sprintf('%s/storage/%s', $almsBaseUrl, ltrim($UserProfile, '/'));

        // Default fallback image
        $fallbackImage = asset('assets/img/profile.png');

        // Check if the file exists on the remote server
        $finalImageLink = $fallbackImage; // Default to fallback image
        try {
            $response = Http::head($imageLink);
            if ($response->ok()) {
                $finalImageLink = $imageLink;
            }
        } catch (\Exception $e) {
            // Log the exception if necessary (optional)
            // \Log::error('Error checking profile picture existence: ' . $e->getMessage());
        }

        // Return the final image link
        return $finalImageLink;
    }

}
