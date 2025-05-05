<?php

namespace App\Helpers;

use Adldap\Laravel\Facades\Adldap;
use Adldap\Utilities;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ActiveDirectoryHelper
{
    /**
     * Get Active Directory lockout configurations.
     *
     * @return array
     */
    public static function getAdLockoutConfigurations(): array
    {
        // Default values to return in case AD query fails
        $defaultThreshold = 0;
        $defaultDurationMinutes = 0;

        try {
            // Search the Active Directory for the root configuration object
            $configuration
            = Adldap::search()
                ->setDn(config('ldap.connections.default.settings.base_dn'))
                ->whereEquals('objectClass', 'domainDNS')
                ->first();

            // If a configuration object is found, retrieve the lockout settings
            if ($configuration) {
                // Retrieve lockout threshold (number of failed attempts before lockout)
                $lockoutThreshold = (int) ($configuration->getFirstAttribute('lockoutThreshold') ?? $defaultThreshold);

                // Retrieve and convert lockout duration (from 100-nanosecond intervals to minutes)
                $lockoutDuration = $configuration->getFirstAttribute('lockoutDuration');
                $lockoutDurationMinutes = $lockoutDuration
                                            ? abs((int) $lockoutDuration) / 600000000 // Convert to minutes
                                            : $defaultDurationMinutes;
            } else {
                // Use default values if no configuration object is found
                $lockoutThreshold = $defaultThreshold;
                $lockoutDurationMinutes = $defaultDurationMinutes;
            }
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error("Error fetching Active Directory lockout configurations: " . $e->getMessage());

            // Use default values in case of an exception
            $lockoutThreshold = $defaultThreshold;
            $lockoutDurationMinutes = $defaultDurationMinutes;
        }

        // Return the lockout configurations as an associative array
        return [
            'threshold' => $lockoutThreshold,
            'duration'  => $lockoutDurationMinutes
        ];
    }

    /**
     * Check if Active Directory account is disabled
     *
     * @param  int  $userAccountControl
     * @return bool
     */
    public static function isAccountDisabled(int $userAccountControl): bool
    {
        // If userAccountControl is null, assume the account is not disabled
        if (is_null($userAccountControl)) {
            return false;
        }

        // Check if the account is disabled by examining the second bit
        return ($userAccountControl & 2);
    }

    /**
     * Check if Active Directory account has expired
     *
     * @param  int  $accountExpires
     * @return bool
     */
    public static function isAccountExpired(int $accountExpires): bool
    {
        // If accountExpires is null, the account never expires but if accountExpires is 0 or 9223372036854775807, the account is not expired
        if (is_null($accountExpires) || $accountExpires == 0 || $accountExpires == 9223372036854775807) {
            return false;
        }

        // Convert the Windows FileTime to Unix timestamp
        $accountExpiresUnix = Utilities::convertWindowsTimeToUnixTime($accountExpires);

        // Check if the account has expired by comparing with the current timestamp
        return Carbon::now()->timestamp > $accountExpiresUnix;
    }

    /**
     * Check if Active Directory account is currently locked
     *
     * @param  int  $lockoutTime
     * @return bool
     */
    public static function isAccountLockedOut(int $lockoutTime): bool
    {
        // If lockoutTime is 0, the account is not locked
        if ($lockoutTime == 0) {
            return false;
        }

        // Convert Windows FileTime to Unix timestamp
        $lockoutTimestamp = Utilities::convertWindowsTimeToUnixTime($lockoutTime);

        // Check if the account is locked by comparing with the current timestamp
        return Carbon::now()->timestamp > $lockoutTimestamp;
    }

    /**
     * Check if Active Directory account password has expired
     *
     * @param  int  $pwdLastSet
     * @return bool
     */
    public static function isAccountPasswordExpired(?int $userAccountControl, ?int $pwdLastSet): bool
    {
        // If pwdLastSet is null or 0, assume the password is not set (not expired)
        if (empty($pwdLastSet)) {
            return false;
        }

        // If the account has the "DONT_EXPIRE_PASSWORD" flag, the password never expires
        if (!is_null($userAccountControl) && ($userAccountControl & 0x10000)) {
            return false;
        }

        // Convert Windows FileTime to Unix timestamp
        $pwdLastSetTimestamp = Utilities::convertWindowsTimeToUnixTime($pwdLastSet);

        // Get maximum password age from config (default to 90 days if not set)
        $maxPasswordAgeDays = config('main.max_password_age_days', 90);
        if (!is_numeric($maxPasswordAgeDays) || $maxPasswordAgeDays <= 0) {
            throw new \InvalidArgumentException('Invalid max password age configuration.');
        }

        // Calculate expiration timestamp
        $expiryTimestamp = $pwdLastSetTimestamp + ($maxPasswordAgeDays * 86400); // 86400 = 24 * 60 * 60

        // Check if the password is expired
        return Carbon::now()->timestamp > $expiryTimestamp;
    }
}
