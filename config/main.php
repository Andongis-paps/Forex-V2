<?php

return [
    'company_name'                  => 'Sinag Pawnshop Corporation',
    'app_url_name'                  => 'forex',   // Set the application url name based on the DNS from the server and use it in access.`tblapplications` database
    'max_password_age_days'         => 90,                  // Set the maximum password age in days based on AD policy
    'enable_account_deactivation'   => true,                // Allow user to deactive their account
    'enable_change_password'        => true,                // Allow user to change their password
    'enable_change_security_code'   => true                 // Allow user to change their security code
];
