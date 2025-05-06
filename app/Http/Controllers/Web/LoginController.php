<?php

namespace App\Http\Controllers\Web;
use App;
use Lang;
use Session;
use App\Admin;
use Validator;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Helpers\MenuManagement;
use Adldap\Laravel\Facades\Adldap;
use Illuminate\Support\Facades\DB;
use App\Helpers\ServerAddressHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Helpers\ActiveDirectoryHelper;
use App\Helpers\ApplicationManagement;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\AuthenticateRequest;

class LoginController extends Controller {
    public function login(Request $request) {
        if (!Auth::check()) {
            return view('user.login');
        } else {
            $branch_menus = MenuManagement::getBranchMenus();

            if (count($branch_menus) > 0) {
                $first_branch_menu       = $branch_menus[0];
                $first_branch_menu_url   = str_replace('BRANCH/', '', $first_branch_menu->URLName);

                return redirect()->to(strtolower($first_branch_menu_url));
            }

            $admin_menus = MenuManagement::getAdminMenus();

            if (count($admin_menus) > 0) {
                $first_admin_menu       = $admin_menus[0];

                return redirect()->to(strtolower($first_admin_menu->URLName));
            }
        }
    }

    public function authenticateBackup(AuthenticateRequest $request) {
        $validated = $request->validated();

        $username = $validated['username'];
        $password = $validated['password'];

        $current_ip_address         = $request->ip();
        $current_datetime           = Carbon::now();
        $uams_change_password_url   = ApplicationManagement::getApplicationURLBaseOnServer('uams') . '/user/change-password';

        $adUser     = Adldap::search()->users()->where('samaccountname', '=', $username)->first();
        $dbUser     = User::where('Username', $username)->first();
        $session    = DB::connection('laravelsysconfigs')
                            ->table('tblxsessions')
                            ->where('software_id', config('app.software_id'))
                            ->first();

        $remaining_time = '';

        $total_attempts = 0;

        if ($dbUser) {
            $login_attempt = DB::connection('laravelsysconfigs')
                ->table('login_attempts')
                ->select('TotalAttempts', 'LockedUntil')
                ->where('UserID', $dbUser->UserID)
                ->first();
        }

        if ($adUser) {
            if (Adldap::auth()->attempt($username, $password)) {
                if ($dbUser) {
                    if ($dbUser->HashPassword) {
                        if (Hash::check($password, $dbUser->HashPassword)) {
                            if ($dbUser->IsSecurityCodeUpdated == 0) {
                                // Log the failed attempt
                                DB::connection('laravelsysconfigs')
                                    ->table('login_history')
                                    ->insert([
                                        'SoftwareID'        => config('app.software_id'),
                                        'ADID'              => $adUser->getConvertedSid(),
                                        'UserID'            => $dbUser->UserID,
                                        'Username'          => $username,
                                        'Password'          => $password,
                                        'IPAddress'         => $current_ip_address,
                                        'Successful'        => 0,
                                        'Event'             => 'Outdated Security Code',
                                        'AttemptedAt'       => $current_datetime,
                                        'CreatedAt'         => $current_datetime,
                                        'UpdatedAt'         => $current_datetime
                                    ]);
                                return redirect()->to($uams_change_password_url . '/' . $dbUser->Username . '/' . env("APPLICATION_URL"))->with([
                                    'login_failed'      => 1,
                                    'title'             => 'Out-Dated Security Code.',
                                    'message'           => 'Please update your security code.'
                                ]);
                            } else {
                                $userAccountControl = $adUser->getFirstAttribute('userAccountControl');
                                $isDisabled = ($userAccountControl & 2) ? 'Yes' : 'No';
                                if ($isDisabled == 'Yes') {
                                    // Log the failed attempt
                                    DB::connection('laravelsysconfigs')
                                        ->table('login_history')
                                        ->insert([
                                            'SoftwareID'        => config('app.software_id'),
                                            'ADID'              => $adUser->getConvertedSid(),
                                            'UserID'            => $dbUser->UserID,
                                            'Username'          => $username,
                                            'Password'          => $password,
                                            'IPAddress'         => $current_ip_address,
                                            'Successful'        => 0,
                                            'Event'             => 'Disabled AD Account',
                                            'AttemptedAt'       => $current_datetime,
                                            'CreatedAt'         => $current_datetime,
                                            'UpdatedAt'         => $current_datetime
                                        ]);

                                    return redirect()->back()->with([
                                        'login_failed'      => 1,
                                        'title'             => 'Login Failed',
                                        'message'           => 'Your account has been disabled.'
                                    ]);
                                }

                                $accountExpires = $adUser->getFirstAttribute('accountExpires');
                                // 0 or 9223372036854775807 means the account never expires
                                if ($accountExpires != 0 || $accountExpires != 9223372036854775807) {
                                    // Convert Windows FileTime to Unix time and format date
                                    $accountExpires = \Adldap\Utilities::convertWindowsTimeToUnixTime($accountExpires);
                                    $isAccountExpired = now()->timestamp > $accountExpires ? 'Yes' : 'No'; // Check if account is expired

                                    if ($isAccountExpired == 'Yes') {
                                        // Log the failed attempt
                                        DB::connection('laravelsysconfigs')
                                            ->table('login_history')
                                            ->insert([
                                                'SoftwareID'        => config('app.software_id'),
                                                'ADID'              => $adUser->getConvertedSid(),
                                                'UserID'            => $dbUser->UserID,
                                                'Username'          => $username,
                                                'Password'          => $password,
                                                'IPAddress'         => $current_ip_address,
                                                'Successful'        => 0,
                                                'Event'             => 'Expired AD Account',
                                                'AttemptedAt'       => $current_datetime,
                                                'CreatedAt'         => $current_datetime,
                                                'UpdatedAt'         => $current_datetime
                                            ]);

                                        return redirect()->back()->with([
                                            'login_failed'  => 1,
                                            'title'         => 'Login Failed',
                                            'message'       => 'Your account has expired.'
                                        ]);
                                    }
                                }

                                $lockoutTime = $adUser->getFirstAttribute('lockoutTime');
                                if ($lockoutTime != 0) {
                                    $lockoutTimestamp = \Adldap\Utilities::convertWindowsTimeToUnixTime($lockoutTime);
                                    $isLocked = now()->timestamp > $lockoutTimestamp ? 'Yes' : 'No';

                                    if ($isLocked == 'Yes') {
                                        // Log the failed attempt
                                        DB::connection('laravelsysconfigs')
                                            ->table('login_history')
                                            ->insert([
                                                'SoftwareID'        => config('app.software_id'),
                                                'ADID'              => $adUser->getConvertedSid(),
                                                'UserID'            => $dbUser->UserID,
                                                'Username'          => $username,
                                                'Password'          => $password,
                                                'IPAddress'         => $current_ip_address,
                                                'Successful'        => 0,
                                                'Event'             => 'Locked AD Account',
                                                'AttemptedAt'       => $current_datetime,
                                                'CreatedAt'         => $current_datetime,
                                                'UpdatedAt'         => $current_datetime
                                            ]);

                                        return redirect()->back()->with([
                                            'login_failed'      => 1,
                                            'title'             => 'Login Failed',
                                            'message'           => 'Your account has been locked.'
                                        ]);
                                    }
                                }

                                $pwdLastSet = $adUser->getFirstAttribute('pwdLastSet');
                                $hasPasswordExpiry = ($adUser->getFirstAttribute('userAccountControl') & 0x10000) ? false : true;

                                $currentDateTime = now(); // Get current date and time
                                if ($pwdLastSet != 0) {
                                    // Convert Windows FileTime to Unix time
                                    $pwdLastSetTimestamp = \Adldap\Utilities::convertWindowsTimeToUnixTime($pwdLastSet);

                                    // Set the maximum password age (in days) based on your AD policy
                                    $maxPasswordAgeDays = 90; // Example: 90 days
                                    $maxPasswordAgeSeconds = $maxPasswordAgeDays * 24 * 60 * 60; // Convert days to seconds

                                    // Calculate expiry date
                                    $expiryTimestamp = $pwdLastSetTimestamp + $maxPasswordAgeSeconds;

                                    // Check if the password is expired
                                    $isPasswordExpired = $currentDateTime->timestamp > $expiryTimestamp;

                                    if ($isPasswordExpired && $hasPasswordExpiry) {
                                        // Log the failed attempt
                                        DB::connection('laravelsysconfigs')
                                            ->table('login_history')
                                            ->insert([
                                                'SoftwareID'        => config('app.software_id'),
                                                'ADID'              => $adUser->getConvertedSid(),
                                                'UserID'            => $dbUser->UserID,
                                                'Username'          => $username,
                                                'Password'          => $password,
                                                'IPAddress'         => $current_ip_address,
                                                'Successful'        => 0,
                                                'Event'             => 'Expired AD Password',
                                                'AttemptedAt'       => $current_datetime,
                                                'CreatedAt'         => $current_datetime,
                                                'UpdatedAt'         => $current_datetime
                                            ]);

                                        return redirect()->back()->with([
                                            'login_failed'      => 1,
                                            'title'             => 'Login Failed',
                                            'message'           => 'Your password has expired.'
                                        ]);
                                    }
                                }

                                $userStatus = $dbUser->Status;
                                if ($userStatus == 0 || $userStatus == 2) {
                                    // Log the failed attempt
                                    DB::connection('laravelsysconfigs')
                                        ->table('login_history')
                                        ->insert([
                                            'SoftwareID'        => config('app.software_id'),
                                            'ADID'              => $adUser->getConvertedSid(),
                                            'UserID'            => $dbUser->UserID,
                                            'Username'          => $username,
                                            'Password'          => $password,
                                            'IPAddress'         => $current_ip_address,
                                            'Successful'        => 0,
                                            'Event'             => $userStatus == 2 ? 'Suspended DB Account' : 'Inactive DB Account',
                                            'AttemptedAt'       => $current_datetime,
                                            'CreatedAt'         => $current_datetime,
                                            'UpdatedAt'         => $current_datetime
                                        ]);

                                    return redirect()->back()->with([
                                        'login_failed'      => 1,
                                        'title'             => 'Login Failed',
                                        'message'           => $userStatus == 2 ? 'Your account has been suspended.' : 'Your account has been inactive.'
                                    ]);
                                }

                                if ($login_attempt) {
                                    // Check if the user is locked out
                                    if ($login_attempt->LockedUntil && Carbon::parse($login_attempt->LockedUntil)->isFuture()) {
                                        $remaining_time = $this->getRemainingTime($login_attempt->LockedUntil, $current_datetime);

                                        // Log the failed attempt
                                        DB::connection('laravelsysconfigs')
                                            ->table('login_history')
                                            ->insert([
                                                'SoftwareID'        => config('app.software_id'),
                                                'ADID'              => $adUser->getConvertedSid(),
                                                'UserID'            => $dbUser->UserID,
                                                'Username'          => $username,
                                                'Password'          => $password,
                                                'IPAddress'         => $current_ip_address,
                                                'Successful'        => 0,
                                                'Event'             => 'Locked DB Account',
                                                'AttemptedAt'       => $current_datetime,
                                                'CreatedAt'         => $current_datetime,
                                                'UpdatedAt'         => $current_datetime
                                            ]);

                                        return redirect()->back()->with([
                                            'login_failed'  => 1,
                                            'title'         => 'Login Failed',
                                            'message'       => 'Too many login attempts. Your account has been temporarily locked. Please try again in ' . $remaining_time . '.'
                                        ]);
                                    }

                                    // Reset attempts if the lockout period has expired
                                    if ($login_attempt->LockedUntil && Carbon::parse($login_attempt->LockedUntil)->isPast()) {
                                        DB::connection('laravelsysconfigs')
                                            ->table('login_attempts')
                                            ->where('UserID', $dbUser->UserID)
                                            ->delete();

                                        $login_attempt = null;
                                    }
                                }

                                Auth::login($dbUser);

                                if (MenuManagement::totalPermissions() > 0) {
                                    session(['Rset' => 'B']);

                                    // Delete login attempts on successful login
                                    DB::connection('laravelsysconfigs')
                                        ->table('login_attempts')
                                        ->where('UserID', $dbUser->UserID)
                                        ->delete();

                                    // Log the success attempt
                                    DB::connection('laravelsysconfigs')
                                        ->table('login_history')
                                        ->insert([
                                            'SoftwareID'        => config('app.software_id'),
                                            'ADID'              => $adUser->getConvertedSid(),
                                            'UserID'            => $dbUser->UserID,
                                            'Username'          => $username,
                                            'Password'          => $password,
                                            'IPAddress'         => $current_ip_address,
                                            'Successful'        => 1,
                                            'Event'             => null,
                                            'AttemptedAt'       => $current_datetime,
                                            'CreatedAt'         => $current_datetime,
                                            'UpdatedAt'         => $current_datetime
                                        ]);


                                    $branch_menus = MenuManagement::getBranchMenus();

                                    if (count($branch_menus) > 0) {
                                        $first_branch_menu       = $branch_menus[0];
                                        $first_branch_menu_url   = str_replace('BRANCH/', '', $first_branch_menu->URLName);


                                        return redirect()->intended(strtolower($first_branch_menu_url))->with([
                                            'login_success' => 1,
                                            'title'         => 'Login Success',
                                            'message'       => "You are now logged in."
                                        ]);
                                    }

                                    $admin_menus = MenuManagement::getAdminMenus();

                                    if (count($admin_menus) > 0) {
                                        $first_admin_menu       = $admin_menus[0];

                                        return redirect()->intended(strtolower($first_admin_menu->URLName))->with([
                                            'login_success' => 1,
                                            'title'         => 'Login Success',
                                            'message'       => "You are now logged in."
                                        ]);
                                    }
                                } else {
                                    Auth::logout();

                                    $request->session()->invalidate();
                                    $request->session()->regenerateToken();

                                    // Log the failed attempt
                                    DB::connection('laravelsysconfigs')
                                        ->table('login_history')
                                        ->insert([
                                            'SoftwareID'        => config('app.software_id'),
                                            'ADID'              => $adUser->getConvertedSid(),
                                            'UserID'            => $dbUser->UserID,
                                            'Username'          => $username,
                                            'Password'          => $password,
                                            'IPAddress'         => $current_ip_address,
                                            'Successful'        => 0,
                                            'Event'             => 'No Permission',
                                            'AttemptedAt'       => $current_datetime,
                                            'CreatedAt'         => $current_datetime,
                                            'UpdatedAt'         => $current_datetime
                                        ]);

                                    return redirect()->back()->with([
                                        'login_failed'      => 1,
                                        'title'             => 'Unauthorized',
                                        'message'           => 'You have no permission to access this system.'
                                    ]);
                                }
                            }
                        } else {
                            // Log the failed attempt
                            DB::connection('laravelsysconfigs')
                                ->table('login_history')
                                ->insert([
                                    'SoftwareID'        => config('app.software_id'),
                                    'ADID'              => $adUser->getConvertedSid(),
                                    'UserID'            => $dbUser->UserID,
                                    'Username'          => $username,
                                    'Password'          => $password,
                                    'IPAddress'         => $current_ip_address,
                                    'Successful'        => 0,
                                    'Event'             => 'Incorrect DB Password',
                                    'AttemptedAt'       => $current_datetime,
                                    'CreatedAt'         => $current_datetime,
                                    'UpdatedAt'         => $current_datetime
                                ]);

                            DB::connection('pawnshop')->select('CALL spa_update_user_credentials(?, ?, ?)', [$username, $password, Hash::make($password)]);

                            // return redirect()->to($uams_change_password_url . '/' . $dbUser->UserID)->with([
                            return redirect()->to($uams_change_password_url . '/' . $dbUser->Username . '/' . env("APPLICATION_URL"))->with([
                                'login_failed'      => 1,
                                'title'             => 'Login Failed',
                                'message'           => 'Please match your system and Active Directory (AD) credentials.'
                            ]);
                        }
                    } else {
                        // Log the failed attempt
                        DB::connection('laravelsysconfigs')
                            ->table('login_history')
                            ->insert([
                                'SoftwareID'        => config('app.software_id'),
                                'ADID'              => $adUser->getConvertedSid(),
                                'UserID'            => $dbUser->UserID,
                                'Username'          => $username,
                                'Password'          => $password,
                                'IPAddress'         => $current_ip_address,
                                'Successful'        => 0,
                                'Event'             => 'NULL DB Password',
                                'AttemptedAt'       => $current_datetime,
                                'CreatedAt'         => $current_datetime,
                                'UpdatedAt'         => $current_datetime
                            ]);

                        DB::connection('pawnshop')->select('CALL spa_update_user_credentials(?, ?, ?)', [$username, $password, Hash::make($password)]);

                        // return redirect()->to($uams_change_password_url . '/' . $dbUser->UserID)->with([
                        return redirect()->to($uams_change_password_url . '/' . $dbUser->Username . '/' . env("APPLICATION_URL"))->with([
                            'login_failed'      => 1,
                            'title'             => 'Login Failed',
                            'message'           => 'Please match your system and Active Directory (AD) credentials.'
                        ]);
                    }
                } else {
                    // Log the failed attempt
                    DB::connection('laravelsysconfigs')
                        ->table('login_history')
                        ->insert([
                            'SoftwareID'        => config('app.software_id'),
                            'ADID'              => $adUser->getConvertedSid(),
                            'UserID'            => null,
                            'Username'          => $username,
                            'Password'          => $password,
                            'IPAddress'         => $current_ip_address,
                            'Successful'        => 0,
                            'Event'             => 'Incorrect DB Username',
                            'AttemptedAt'       => $current_datetime,
                            'CreatedAt'         => $current_datetime,
                            'UpdatedAt'         => $current_datetime
                        ]);

                    return redirect()->back()->with([
                        'login_failed'  => 1,
                        'title'         => 'Login Failed',
                        'message'       => 'You have no existing system account.'
                    ]);
                }
            } else {
                $userAccountControl = $adUser->getFirstAttribute('userAccountControl');
                $isDisabled = ($userAccountControl & 2) ? 'Yes' : 'No';
                if ($isDisabled == 'Yes') {
                    // Log the failed attempt
                    DB::connection('laravelsysconfigs')
                        ->table('login_history')
                        ->insert([
                            'SoftwareID'        => config('app.software_id'),
                            'ADID'              => $adUser->getConvertedSid(),
                            'UserID'            => $dbUser ? $dbUser->UserID : null,
                            'Username'          => $username,
                            'Password'          => $password,
                            'IPAddress'         => $current_ip_address,
                            'Successful'        => 0,
                            'Event'             => 'Disabled AD Account',
                            'AttemptedAt'       => $current_datetime,
                            'CreatedAt'         => $current_datetime,
                            'UpdatedAt'         => $current_datetime
                        ]);

                    return redirect()->back()->with([
                        'login_failed'      => 1,
                        'title'             => 'Login Failed',
                        'message'           => 'Your account has been disabled.'
                    ]);
                }

                $accountExpires = $adUser->getFirstAttribute('accountExpires');
                // 0 or 9223372036854775807 means the account never expires
                if ($accountExpires != 0 || $accountExpires != 9223372036854775807) {
                    // Convert Windows FileTime to Unix time and format date
                    $accountExpires = \Adldap\Utilities::convertWindowsTimeToUnixTime($accountExpires);
                    $isAccountExpired = now()->timestamp > $accountExpires ? 'Yes' : 'No'; // Check if account is expired

                    if ($isAccountExpired == 'Yes') {
                        // Log the failed attempt
                        DB::connection('laravelsysconfigs')
                            ->table('login_history')
                            ->insert([
                                'SoftwareID'        => config('app.software_id'),
                                'ADID'              => $adUser->getConvertedSid(),
                                'UserID'            => $dbUser ? $dbUser->UserID : null,
                                'Username'          => $username,
                                'Password'          => $password,
                                'IPAddress'         => $current_ip_address,
                                'Successful'        => 0,
                                'Event'             => 'Expired AD Account',
                                'AttemptedAt'       => $current_datetime,
                                'CreatedAt'         => $current_datetime,
                                'UpdatedAt'         => $current_datetime
                            ]);

                        return redirect()->back()->with([
                            'login_failed'  => 1,
                            'title'         => 'Login Failed',
                            'message'       => 'Your account has expired.'
                        ]);
                    }
                }

                $lockoutTime = $adUser->getFirstAttribute('lockoutTime');
                if ($lockoutTime > 0) {
                    $lockoutTimestamp = \Adldap\Utilities::convertWindowsTimeToUnixTime($lockoutTime);
                    $isLocked = now()->timestamp > $lockoutTimestamp ? 'Yes' : 'No';

                    if ($isLocked == 'Yes') {
                        // Log the failed attempt
                        DB::connection('laravelsysconfigs')
                            ->table('login_history')
                            ->insert([
                                'SoftwareID'        => config('app.software_id'),
                                'ADID'              => $adUser->getConvertedSid(),
                                'UserID'            => $dbUser ? $dbUser->UserID : null,
                                'Username'          => $username,
                                'Password'          => $password,
                                'IPAddress'         => $current_ip_address,
                                'Successful'        => 0,
                                'Event'             => 'Locked AD Account',
                                'AttemptedAt'       => $current_datetime,
                                'CreatedAt'         => $current_datetime,
                                'UpdatedAt'         => $current_datetime
                            ]);

                        return redirect()->back()->with([
                            'login_failed'      => 1,
                            'title'             => 'Login Failed',
                            'message'           => 'Your account has been locked.'
                        ]);
                    }
                }

                $pwdLastSet = $adUser->getFirstAttribute('pwdLastSet');
                $hasPasswordExpiry = ($adUser->getFirstAttribute('userAccountControl') & 0x10000) ? false : true;

                $currentDateTime = now(); // Get current date and time
                if ($pwdLastSet != 0) {
                    // Convert Windows FileTime to Unix time
                    $pwdLastSetTimestamp = \Adldap\Utilities::convertWindowsTimeToUnixTime($pwdLastSet);

                    // Set the maximum password age (in days) based on your AD policy
                    $maxPasswordAgeDays = 90; // Example: 90 days
                    $maxPasswordAgeSeconds = $maxPasswordAgeDays * 24 * 60 * 60; // Convert days to seconds

                    // Calculate expiry date
                    $expiryTimestamp = $pwdLastSetTimestamp + $maxPasswordAgeSeconds;

                    // Check if the password is expired
                    $isPasswordExpired = $currentDateTime->timestamp > $expiryTimestamp;

                    if ($isPasswordExpired && $hasPasswordExpiry) {
                        // Log the failed attempt
                        DB::connection('laravelsysconfigs')
                            ->table('login_history')
                            ->insert([
                                'SoftwareID'        => config('app.software_id'),
                                'ADID'              => $adUser->getConvertedSid(),
                                'UserID'            => $dbUser ? $dbUser->UserID : null,
                                'Username'          => $username,
                                'Password'          => $password,
                                'IPAddress'         => $current_ip_address,
                                'Successful'        => 0,
                                'Event'             => 'Expired AD Password',
                                'AttemptedAt'       => $current_datetime,
                                'CreatedAt'         => $current_datetime,
                                'UpdatedAt'         => $current_datetime
                            ]);

                        return redirect()->back()->with([
                            'login_failed'      => 1,
                            'title'             => 'Login Failed',
                            'message'           => 'Your password has expired.'
                        ]);
                    }
                }

                // Log the failed attempt
                DB::connection('laravelsysconfigs')
                    ->table('login_history')
                    ->insert([
                        'SoftwareID'        => config('app.software_id'),
                        'ADID'              => $adUser->getConvertedSid(),
                        'UserID'            => $dbUser ? $dbUser->UserID : null,
                        'Username'          => $username,
                        'Password'          => $password,
                        'IPAddress'         => $current_ip_address,
                        'Successful'        => 0,
                        'Event'             => 'Incorrect AD Password',
                        'AttemptedAt'       => $current_datetime,
                        'CreatedAt'         => $current_datetime,
                        'UpdatedAt'         => $current_datetime
                    ]);

                if ($dbUser) {
                    // Increment login attempts
                    if ($login_attempt) {
                        // Check if the user is locked out
                        if ($login_attempt->LockedUntil && Carbon::parse($login_attempt->LockedUntil)->isFuture()) {
                            $remaining_time = $this->getRemainingTime($login_attempt->LockedUntil, $current_datetime);

                            // Log the failed attempt
                            DB::connection('laravelsysconfigs')
                                ->table('login_history')
                                ->insert([
                                    'SoftwareID'        => config('app.software_id'),
                                    'ADID'              => $adUser->getConvertedSid(),
                                    'UserID'            => $dbUser->UserID,
                                    'Username'          => $username,
                                    'Password'          => $password,
                                    'IPAddress'         => $current_ip_address,
                                    'Successful'        => 0,
                                    'Event'             => 'Locked DB Account',
                                    'AttemptedAt'       => $current_datetime,
                                    'CreatedAt'         => $current_datetime,
                                    'UpdatedAt'         => $current_datetime
                                ]);

                            return redirect()->back()->with([
                                'login_failed'  => 1,
                                'title'         => 'Login Failed',
                                'message'       => 'Too many login attempts. Your account has been temporarily locked. Please try again in ' . $remaining_time . '.'
                            ]);
                        }

                        // Reset attempts if the lockout period has expired
                        if ($login_attempt->LockedUntil && Carbon::parse($login_attempt->LockedUntil)->isPast()) {
                            DB::connection('laravelsysconfigs')
                                ->table('login_attempts')
                                ->where('UserID', $dbUser->UserID)
                                ->delete();

                            $login_attempt = null;
                        }

                        if ($login_attempt) {
                            $total_attempts = $login_attempt->TotalAttempts + 1;
                            $locked_until   = $total_attempts >= $session->attemps ? Carbon::now()->addMinutes($session->waiting_time) : null;

                            if ($total_attempts >= $session->attemps) {
                                $remaining_time = $this->getRemainingTime($locked_until, $current_datetime);
                            }

                            DB::connection('laravelsysconfigs')
                                ->table('login_attempts')
                                ->where('UserID', $dbUser->UserID)
                                ->update([
                                    'TotalAttempts' => $total_attempts,
                                    'LockedUntil'   => $locked_until,
                                    'UpdatedAt'     => $current_datetime
                                ]);
                        } else {
                            $total_attempts = 1;

                            // Create a new login attempt record
                            DB::connection('laravelsysconfigs')
                                ->table('login_attempts')
                                ->insert([
                                    'UserID'        => $dbUser->UserID,
                                    'TotalAttempts' => 1,
                                    'LockedUntil'   => null,
                                    'CreatedAt'     => $current_datetime,
                                    'UpdatedAt'     => $current_datetime
                                ]);
                        }

                    } else {
                        $total_attempts = 1;

                        // Create a new login attempt record
                        DB::connection('laravelsysconfigs')
                            ->table('login_attempts')
                            ->insert([
                                'UserID'        => $dbUser->UserID,
                                'TotalAttempts' => 1,
                                'LockedUntil'   => null,
                                'CreatedAt'     => $current_datetime,
                                'UpdatedAt'     => $current_datetime
                            ]);
                    }

                    return redirect()->back()->with([
                        'login_failed'  => 1,
                        'title'         => 'Login Failed',
                        'message'       => $total_attempts >= $session->attemps ? 'Too many login attempts. Your account has been temporarily locked. Please try again in ' . $remaining_time . '.'  : 'Invalid username or password.'
                    ]);
                } else {
                    return redirect()->back()->with([
                        'login_failed'  => 1,
                        'title'         => 'Login Failed',
                        'message'       => 'You have no existing system account.'
                    ]);
                }
            }
        } else {
            // Log the failed attempt
            DB::connection('laravelsysconfigs')
                ->table('login_history')
                ->insert([
                    'SoftwareID'        => config('app.software_id'),
                    'ADID'              => null,
                    'UserID'            => $dbUser ? $dbUser->UserID : null,
                    'Username'          => $username,
                    'Password'          => $password,
                    'IPAddress'         => $current_ip_address,
                    'Successful'        => 0,
                    'Event'             => 'Incorrect AD Username',
                    'AttemptedAt'       => $current_datetime,
                    'CreatedAt'         => $current_datetime,
                    'UpdatedAt'         => $current_datetime
                ]);

            return redirect()->back()->with([
                'login_failed'  => 1,
                'title'         => 'Login Failed',
                'message'       => 'Invalid username or password.'
            ]);
        }
    }

    public function logout(Request $request) {
        $current_datetime   = Carbon::now();

        // Log the logout
        DB::connection('laravelsysconfigs')
            ->table('login_history')
            ->insert([
                'UserID'            => null,
                'Username'          => Auth::user()->Username,
                'Password'          => Auth::user()->Password,
                'IPAddress'         => $request->ip(),
                'Successful'        => 0,
                'Event'             => 'Logged Out',
                'AttemptedAt'       => $current_datetime,
                'CreatedAt'         => $current_datetime,
                'UpdatedAt'         => $current_datetime,
                'SoftwareID'        => config('app.software_id'),
            ]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function sessionExpired(Request $request) {
        $current_datetime   = Carbon::now();

        // Log the logout
        DB::connection('laravelsysconfigs')
            ->table('login_history')
            ->insert([
                'UserID'            => null,
                'Username'          => Auth::user()->Username,
                'Password'          => Auth::user()->Password,
                'IPAddress'         => $request->ip(),
                'Successful'        => 0,
                'Event'             => 'Auto Logged Out',
                'AttemptedAt'       => $current_datetime,
                'CreatedAt'         => $current_datetime,
                'UpdatedAt'         => $current_datetime,
                'SoftwareID'        => config('app.software_id'),
            ]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function getRemainingTimeBackup($from_datetime, $start_datetime) {
        $locked_until    = Carbon::parse($from_datetime);
        $diff_in_seconds = $locked_until->diffInSeconds($start_datetime);

        //  Get the remaining time
        if ($diff_in_seconds < 60) {
            $remaining_time = $diff_in_seconds . ' seconds';
        } elseif ($diff_in_seconds < 3600) {
            $remaining_time = $locked_until->diffInMinutes($start_datetime) . ' minutes';
        } elseif ($diff_in_seconds < 86400) {
            $remaining_time = $locked_until->diffInHours($start_datetime) . ' hours';
        } else {
            $remaining_time = $locked_until->diffInDays($start_datetime) . ' days';
        }

        return $remaining_time;
    }

    public function changeBranch(Request $request) {
        DB::connection('pawnshop')->table('tblxusers')
            ->where('tblxusers.UserID', $request->get('matched_user_id'))
            ->update([
                'BranchCode' => $request->get('branch_code')
            ]);
    }

    public function sendEmail() {
        $message = "Hello, this is a test email!";

        Mail::raw($message, function ($email) {
            $email->to('andongis2016@gmail.com')
                  ->subject('Test Email');
        });

        return response()->json(['message' => 'Email sent successfully']);
    }

    public function authenticate(AuthenticateRequest $request){
        // Validate the incoming request data
        $data = $request->validated();

        $username       = $data['username'];
        $password       = $data['password'];
        $hash_password  = Hash::make($password);

        // Get the current IP address and datetime
        $currentIpAddress   = $request->ip();
        $currentDateTime    = Carbon::now();

        // Initialize attempts-related variables
        $totalAttempts  = 0;
        $remainingTime  = '';

        // Attempt to find the user in Active Directory (AD) and pawnshop database
        $adUser = Adldap::search()->users()->where('samaccountname', '=', $username)->first();
        $dbUser = User::where('pawnshop.tblxusers.Username', '=', $username)->first();

        $softwareId = env('SOFTWARE_ID');

        // Fetch lockout configurations from Active Directory or use defaults
        $lockoutConfigurations = ActiveDirectoryHelper::getAdLockoutConfigurations();

        // Extract the lockout threshold and duration from the lockout configurations
        $lockoutThreshold           = $lockoutConfigurations['threshold'];
        $lockoutDurationInMinutes   = $lockoutConfigurations['duration'];

        if ($adUser) {
            $userAccountControl = $adUser->getFirstAttribute('userAccountControl');
            $accountExpires     = $adUser->getFirstAttribute('accountExpires');
            $lockoutTime        = $adUser->getFirstAttribute('lockoutTime');
            $pwdLastSet         = $adUser->getFirstAttribute('pwdLastSet');
            $badPwdCount        = $adUser->getFirstAttribute('badPwdCount');


            // Check if AD account is disabled
            $isDisabled = ActiveDirectoryHelper::isAccountDisabled($userAccountControl);

            // Check if AD account has expired
            $isAccountExpired = ActiveDirectoryHelper::isAccountExpired($accountExpires);

            // Check if AD account is locked out
            $isAccountLockedOut = ActiveDirectoryHelper::isAccountLockedOut($lockoutTime);

            // Check if AD password has expired
            $isAccountPasswordExpired = ActiveDirectoryHelper::isAccountPasswordExpired($userAccountControl, $pwdLastSet);

            if (Adldap::auth()->attempt($username, $password)) {
                if ($dbUser) {
                    $almsChangePasswordUrl = ServerAddressHelper::getAppUrl('uams') . '/user/change-password/' . $dbUser->Username . '/' . config('main.app_url_name');
                    if ($dbUser->HashPassword) {
                        if (Hash::check($password, $dbUser->HashPassword)) {
                            if ($dbUser->IsSecurityCodeUpdated == 1) {
                                // $employee = $dbUser->employee;

                                // Check if DB account is inactive or suspended
                                $userStatus = $dbUser->Status;
                                if ($userStatus == 0 || $userStatus == 2) {
                                    // Log the failed attempt

                                    DB::connection('laravelsysconfigs')
                                        ->table('login_history')
                                        ->insert([
                                                'SoftwareID'    => $softwareId,
                                                'ADID'          => $adUser->getConvertedSid(),
                                                'UserID'        => $dbUser->UserID,
                                                'Username'      => $username,
                                                'Password'      => $password,
                                                'IPAddress'     => $currentIpAddress,
                                                'Successful'    => 0,
                                                'Event'         => $userStatus == 2 ? 'Suspended DB Account' : 'Inactive DB Account',
                                                'AttemptedAt'   => $currentDateTime,
                                                'CreatedAt'     => $currentDateTime,
                                                'UpdatedAt'     => $currentDateTime
                                            ]);

                                    return response()->json([
                                        'success'  => false,
                                        'type'     => 'error',
                                        'title'    => 'Login Failed',
                                        'message'  => $userStatus == 2 ? 'Your account has been suspended.' : 'Your account has been inactive.'
                                    ], 403);
                                }

                                // if ($employee) {
                                    $hasModuleAccess = $dbUser->hasModuleAccess();

                                    if ($hasModuleAccess) {
                                        // Log in the user
                                        Auth::login($dbUser);

                                        // Log the success attempt
                                        DB::connection('laravelsysconfigs')
                                            ->table('login_history')
                                            ->insert([
                                                        'SoftwareID'    => $softwareId,
                                                        'ADID'          => $adUser->getConvertedSid(),
                                                        'UserID'        => $dbUser->UserID,
                                                        'Username'      => $username,
                                                        'Password'      => $password,
                                                        'IPAddress'     => $currentIpAddress,
                                                        'Successful'    => 1,
                                                        'Event'         => 'Logged In',
                                                        'AttemptedAt'   => $currentDateTime,
                                                        'CreatedAt'     => $currentDateTime,
                                                        'UpdatedAt'     => $currentDateTime
                                                    ]);

                                        // Get the route of the first module the user has access to
                                        // $route = strtolower(str_replace('/', '.', $hasModuleAccess)) . '.index';
                                        $route = strtolower(str_replace('/', '.', $hasModuleAccess));

                                        // Get the intended URL or fallback to the dashboard
                                        $intendedUrl = session()->get('url.intended', route($route));

                                        return response()->json([
                                            'success'       => true,
                                            'type'     => 'success',
                                            'title'    => 'Login Success',
                                            'message'  => 'You have been successfully logged in.',
                                            'redirectUrl'   => $intendedUrl
                                        ], 200);
                                    } else {
                                        // Log the failed attempt
                                        DB::connection('laravelsysconfigs')
                                        ->table('login_history')
                                        ->insert([
                                            'SoftwareID'    => $softwareId,
                                            'ADID'          => $adUser->getConvertedSid(),
                                            'UserID'        => $dbUser->UserID,
                                            'Username'      => $username,
                                            'Password'      => $password,
                                            'IPAddress'     => $currentIpAddress,
                                            'Successful'    => 0,
                                            'Event'         => 'No Permission',
                                            'AttemptedAt'   => $currentDateTime,
                                            'CreatedAt'     => $currentDateTime,
                                            'UpdatedAt'     => $currentDateTime
                                        ]);

                                        return response()->json([
                                            'success'       => false,
                                            'type'     => 'error',
                                            'title'    => 'Login Failed',
                                            'message'  => 'You do not have the necessary permissions to access ' . config('app.name') . '.'
                                        ], 401);
                                    }
                                // } else {
                                //     // Log the failed attempt
                                //     DB::connection('laravelsysconfigs')
                                //     ->table('login_history')
                                //     ->insert([
                                //         'SoftwareID'    => $softwareId,
                                //         'ADID'          => $adUser->getConvertedSid(),
                                //         'UserID'        => $dbUser->UserID,
                                //         'Username'      => $username,
                                //         'Password'      => $password,
                                //         'IPAddress'     => $currentIpAddress,
                                //         'Successful'    => 0,
                                //         'Event'         => 'Missing Employee Data',
                                //         'AttemptedAt'   => $currentDateTime,
                                //         'CreatedAt'     => $currentDateTime,
                                //         'UpdatedAt'     => $currentDateTime
                                //     ]);

                                //     return response()->json([
                                //         'success'       => false,
                                //         'type'     => 'error',
                                //         'title'    => 'Login Failed',
                                //         'message'  => 'Your account is not linked to employee details.'
                                //     ], 401);
                                // }
                            } else {
                                // Log the failed attempt
                                DB::connection('laravelsysconfigs')
                                ->table('login_history')
                                ->insert([
                                    'SoftwareID'    => $softwareId,
                                    'ADID'          => $adUser->getConvertedSid(),
                                    'UserID'        => $dbUser->UserID,
                                    'Username'      => $username,
                                    'Password'      => $password,
                                    'IPAddress'     => $currentIpAddress,
                                    'Successful'    => 0,
                                    'Event'         => 'Outdated Security Code',
                                    'AttemptedAt'   => $currentDateTime,
                                    'CreatedAt'     => $currentDateTime,
                                    'UpdatedAt'     => $currentDateTime
                                ]);

                                return response()->json([
                                    'success'       => false,
                                    'type'     => 'error',
                                    'title'    => 'Outdated Security Code',
                                    'message'  => 'Please update your security code.',
                                    'redirectUrl'   => $almsChangePasswordUrl
                                ], 401);
                            }
                        } else {
                            // Log the failed attempt
                            DB::connection('laravelsysconfigs')
                                ->table('login_history')
                                ->insert([
                                'SoftwareID'    => $softwareId,
                                'ADID'          => $adUser->getConvertedSid(),
                                'UserID'        => $dbUser->UserID,
                                'Username'      => $username,
                                'Password'      => $password,
                                'IPAddress'     => $currentIpAddress,
                                'Successful'    => 0,
                                'Event'         => 'Incorrect DB Password',
                                'AttemptedAt'   => $currentDateTime,
                                'CreatedAt'     => $currentDateTime,
                                'UpdatedAt'     => $currentDateTime
                            ]);

                            DB::connection('pawnshop')
                                ->select('CALL spa_update_user_credentials(?, ?, ?)', [
                                    $username,
                                    $password,
                                    $hash_password
                                ]);

                            return response()->json([
                                'success'       => false,
                                'type'     => 'error',
                                'title'    => 'Login Failed',
                                'message'  => 'Please match your system and Active Directory (AD) credentials.',
                                'redirectUrl'   => $almsChangePasswordUrl
                            ], 401);
                        }
                    } else {
                        // Log the failed attempt
                        DB::connection('laravelsysconfigs')
                            ->table('login_history')
                            ->insert([
                            'SoftwareID'    => $softwareId,
                            'ADID'          => $adUser->getConvertedSid(),
                            'UserID'        => $dbUser->UserID,
                            'Username'      => $username,
                            'Password'      => $password,
                            'IPAddress'     => $currentIpAddress,
                            'Successful'    => 0,
                            'Event'         => 'NULL DB Password',
                            'AttemptedAt'   => $currentDateTime,
                            'CreatedAt'     => $currentDateTime,
                            'UpdatedAt'     => $currentDateTime
                        ]);

                        DB::connection('pawnshop')
                            ->select('CALL spa_update_user_credentials(?, ?, ?)', [
                                $username,
                                $password,
                                $hash_password
                            ]);

                        return response()->json([
                            'success'       => false,
                            'type'     => 'error',
                            'title'    => 'Login Failed',
                            'message'  => 'Please match your system and Active Directory (AD) credentials.',
                            'redirectUrl'   => $almsChangePasswordUrl
                        ], 401);
                    }
                } else {
                    DB::connection('laravelsysconfigs')
                    ->table('login_history')
                    ->insert([
                                    'SoftwareID'    => $softwareId,
                                    'ADID'          => $adUser->getConvertedSid(),
                                    'UserID'        => null,
                                    'Username'      => $username,
                                    'Password'      => $password,
                                    'IPAddress'     => $currentIpAddress,
                                    'Successful'    => 0,
                                    'Event'         => 'Incorrect DB Username',
                                    'AttemptedAt'   => $currentDateTime,
                                    'CreatedAt'     => $currentDateTime,
                                    'UpdatedAt'     => $currentDateTime
                                ]);

                    return response()->json([
                        'success'       => false,
                        'type'     => 'error',
                        'title'    => 'Login Failed',
                        'message'  => 'You do not have an existing system account.'
                    ], 401);
                }
            } else {
                // Check if AD account is disabled
                if ($isDisabled) {
                    // Log the failed attempt
                    DB::connection('laravelsysconfigs')
                    ->table('login_history')
                    ->insert([
                                    'SoftwareID'    => $softwareId,
                                    'ADID'          => $adUser->getConvertedSid(),
                                    'UserID'        => $dbUser ? $dbUser->UserID : null,
                                    'Username'      => $username,
                                    'Password'      => $password,
                                    'IPAddress'     => $currentIpAddress,
                                    'Successful'    => 0,
                                    'Event'         => 'Disabled AD Account',
                                    'AttemptedAt'   => $currentDateTime,
                                    'CreatedAt'     => $currentDateTime,
                                    'UpdatedAt'     => $currentDateTime
                                ]);

                    return response()->json([
                        'success'       => false,
                        'type'     => 'error',
                        'title'    => 'Login Failed',
                        'message'  => 'Your account has been disabled.'
                    ], 403);
                }

                // Check if AD account has expired
                if ($isAccountExpired) {
                    // Log the failed attempt
                    DB::connection('laravelsysconfigs')
                    ->table('login_history')
                    ->insert([
                                    'SoftwareID'    => $softwareId,
                                    'ADID'          => $adUser->getConvertedSid(),
                                    'UserID'        => $dbUser ? $dbUser->UserID : null,
                                    'Username'      => $username,
                                    'Password'      => $password,
                                    'IPAddress'     => $currentIpAddress,
                                    'Successful'    => 0,
                                    'Event'         => 'Expired AD Account',
                                    'AttemptedAt'   => $currentDateTime,
                                    'CreatedAt'     => $currentDateTime,
                                    'UpdatedAt'     => $currentDateTime
                                ]);

                    return response()->json([
                        'success'       => false,
                        'type'     => 'error',
                        'title'    => 'Login Failed',
                        'message'  => 'Your account has expired.'
                    ], 403);
                }

                // Check if AD account is locked out
                if ($isAccountLockedOut) {
                    // Get the unlock time
                    $lockoutCarbonTimestamp = Carbon::createFromTimestamp(($lockoutTime / 10000000) - 11644473600);
                    $unlockTime             = $lockoutCarbonTimestamp->addMinutes($lockoutDurationInMinutes);
                    $remainingTime          = $this->getRemainingTime($currentDateTime, $unlockTime);

                    // Log the failed attempt
                    DB::connection('laravelsysconfigs')
                    ->table('login_history')
                    ->insert([
                                    'SoftwareID'    => $softwareId,
                                    'ADID'          => $adUser->getConvertedSid(),
                                    'UserID'        => $dbUser ? $dbUser->UserID : null,
                                    'Username'      => $username,
                                    'Password'      => $password,
                                    'IPAddress'     => $currentIpAddress,
                                    'Successful'    => 0,
                                    'Event'         => 'Locked AD Account',
                                    'AttemptedAt'   => $currentDateTime,
                                    'CreatedAt'     => $currentDateTime,
                                    'UpdatedAt'     => $currentDateTime
                                ]);

                    return response()->json([
                        'success'       => false,
                        'type'     => 'error',
                        'title'    => 'Login Failed',
                        'message'  => "Your account has been temporarily locked due to multiple failed login attempts. Please try again after {$remainingTime}."
                    ], 403);
                }

                // Check if AD password has expired
                if ($isAccountPasswordExpired) {
                    // Log the failed attempt
                    DB::connection('laravelsysconfigs')
                    ->table('login_history')
                    ->insert([
                                    'SoftwareID'    => $softwareId,
                                    'ADID'          => $adUser->getConvertedSid(),
                                    'UserID'        => $dbUser ? $dbUser->UserID : null,
                                    'Username'      => $username,
                                    'Password'      => $password,
                                    'IPAddress'     => $currentIpAddress,
                                    'Successful'    => 0,
                                    'Event'         => 'Expired AD Password',
                                    'AttemptedAt'   => $currentDateTime,
                                    'CreatedAt'     => $currentDateTime,
                                    'UpdatedAt'     => $currentDateTime
                                ]);

                    return response()->json([
                        'success'       => false,
                        'type'     => 'error',
                        'title'    => 'Login Failed',
                        'message'  => 'Your password has expired.'
                    ], 403);
                }

                // Log the failed attempt
                DB::connection('laravelsysconfigs')
                    ->table('login_history')
                    ->insert([
                                'SoftwareID'    => $softwareId,
                                'ADID'          => $adUser->getConvertedSid(),
                                'UserID'        => $dbUser ? $dbUser->UserID : null,
                                'Username'      => $username,
                                'Password'      => $password,
                                'IPAddress'     => $currentIpAddress,
                                'Successful'    => 0,
                                'Event'         => 'Incorrect AD Password',
                                'AttemptedAt'   => $currentDateTime,
                                'CreatedAt'     => $currentDateTime,
                                'UpdatedAt'     => $currentDateTime
                            ]);

                if (!is_null($lockoutThreshold) && $lockoutThreshold > 0) {
                    // Get the total count of failed attempts
                    $totalAttempts = $badPwdCount + 1;

                    // Check if the bad password count exceeds the defined threshold
                    if ($totalAttempts >= $lockoutThreshold) {
                        $lockedUntil    = $currentDateTime->copy()->addMinutes($lockoutDurationInMinutes);
                        $remainingTime  = $this->getRemainingTime($currentDateTime, $lockedUntil);
                    }
                }

                return response()->json([
                    'success'       => false,
                    'type'     => 'error',
                    'title'    => 'Login Failed',
                    'message'  => !is_null($lockoutThreshold) && $lockoutThreshold > 0 && $totalAttempts >= $lockoutThreshold ? "Your account has been temporarily locked due to multiple failed login attempts. Please try again in {$remainingTime}."  : 'Invalid username or password. Please use your computer login.'
                ], 401);
            }
        } else {
            // Log the failed attempt
            DB::connection('laravelsysconfigs')
                    ->table('login_history')
                    ->insert([
                            'SoftwareID'    => $softwareId,
                            'ADID'          => null,
                            'UserID'        => $dbUser ? $dbUser->UserID : null,
                            'Username'      => $username,
                            'Password'      => $password,
                            'IPAddress'     => $currentIpAddress,
                            'Successful'    => 0,
                            'Event'         => 'Incorrect AD Username',
                            'AttemptedAt'   => $currentDateTime,
                            'CreatedAt'     => $currentDateTime,
                            'UpdatedAt'     => $currentDateTime
                        ]);

            return response()->json([
                'success'       => false,
                'type'     => 'error',
                'title'    => 'Login Failed',
                'message'  => 'Invalid username or password. Please use your computer login.'
            ], 401);
        }
    }

    private function getRemainingTime($fromDateTime, $toDateTime) {
        // Ensure the input is a Carbon instance
        $from = $fromDateTime instanceof Carbon ? $fromDateTime : Carbon::parse($fromDateTime);
        $to = $toDateTime instanceof Carbon ? $toDateTime : Carbon::parse($toDateTime);

        // Get the difference in seconds
        $diffInSeconds = $to->diffInSeconds($from);

        // Get the remaining time based on the difference
        if ($diffInSeconds < 60) {
            return $diffInSeconds . ' seconds';
        }

        $diff = $to->diff($from); // Use diff() for the DateInterval object

        if ($diff->h > 0 || $diff->d > 0) {
            // For hours and days
            $remainingTime = $this->formatTime($diff->d, 'day') . ' ' . $this->formatTime($diff->h, 'hour');
        } elseif ($diff->i > 0) {
            // For minutes
            $remainingTime = $this->formatTime($diff->i, 'minute');
        } else {
            // Default case for seconds, already handled above
            $remainingTime = $diffInSeconds . ' seconds';
        }

        return $remainingTime;
    }

    private function formatTime($value, $unit) {
        return $value . ' ' . Str::plural($unit, $value);
    }
}
