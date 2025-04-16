<?php

namespace App\Helpers;

use Adldap\Laravel\Facades\Adldap;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ADAccountsManagement
{
    public static function active()
    {
        $ad_accounts = Adldap::search()
                            ->users()
                            ->whereEnabled() // Only fetch enabled (non-disabled) accounts
                            ->whereHas('accountExpires', '>=', now()->timestamp) // Ensure account is not expired
                            ->get();

        return $ad_accounts;
    }

    public static function getUserByUserName($username)
    {
        $ad_account = Adldap::search()->users()->where('samaccountname', '=', $username)->first();

        return $ad_account;
    }
}
