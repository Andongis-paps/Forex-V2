<?php

use Illuminate\Support\Str;

    // Get the current server IP address using the IPAddressManagement helpers function
    $currentIpAddress = getServerIpAddress();
    // $currentIpAddress = '192.168.190.107';

    // Determine the host based on the current server IP address
    if ($currentIpAddress === '192.168.190.107') {
        $host = '192.168.68.1'; // Dev server
    } else if ($currentIpAddress === '192.168.189.107') {
        $host = '192.168.69.1'; // Staging server
    } else if ($currentIpAddress === '192.168.188.107') {
        $host = '192.168.70.2'; // Live server
    } else {
        $host = '192.168.68.1'; // Default to dev server
    }

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        // For forex DB connection
        'forex' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => $host,
            'port' => env('DB_PORT_FOREX', '3306'),
            'database' => env('DB_DATABASE_FOREX', 'forex'),
            'username' => env('DB_USERNAME_FOREX', 'gold'),
            'password' => env('DB_PASSWORD_FOREX', 'sinagjmsmis'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // For pawnshop DB connection
        'pawnshop' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => $host,
            'port' => env('DB_PORT_PAWNSHOP', '3306'),
            'database' => env('DB_DATABASE_PAWNSHOP', 'pawnshop'),
            'username' => env('DB_USERNAME_PAWNSHOP', 'gold'),
            'password' => env('DB_PASSWORD_PAWNSHOP', 'sinagjmsmis'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

         // For forexcurrency DB connection
        'forexcurrency' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => $host,
            'port' => env('DB_PORT_FOREXCURRENCY', '3306'),
            'database' => env('DB_DATABASE_FOREXCURRENCY', 'forexcurrency'),
            'username' => env('DB_USERNAME_FOREXCURRENCY', 'gold'),
            'password' => env('DB_PASSWORD_FOREXCURRENCY', 'sinagjmsmis'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // For CIS DB connection
        'cis' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => $host,
            'port' => env('DB_PORT_CIS', '3306'),
            'database' => env('DB_DATABASE_CIS', 'cis'),
            'username' => env('DB_USERNAME_CIS', 'gold'),
            'password' => env('DB_PASSWORD_CIS', 'sinagjmsmis'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // For HRIS DB connection
        'hris' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => $host,
            'port' => env('DB_PORT_HRIS', '3306'),
            'database' => env('DB_DATABASE_HRIS', 'hris'),
            'username' => env('DB_USERNAME_HRIS', 'gold'),
            'password' => env('DB_PASSWORD_HRIS', 'sinagjmsmis'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // For Accounting DB connection
        'accounting' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => $host,
            'port' => env('DB_PORT_ACCOUNTING', '3306'),
            'database' => env('DB_DATABASE_ACCOUNTING', 'accounting'),
            'username' => env('DB_USERNAME_ACCOUNTING', 'gold'),
            'password' => env('DB_PASSWORD_ACCOUNTING', 'sinagjmsmis'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // For IT Inventory DB connection
        'itinventory' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => $host,
            'port' => env('DB_PORT_ITINVENTORY', '3306'),
            'database' => env('DB_DATABASE_ITINVENTORY', 'itinventory'),
            'username' => env('DB_USERNAME_ITINVENTORY', 'gold'),
            'password' => env('DB_PASSWORD_ITINVENTORY', 'sinagjmsmis'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // For Tracking DB connection
        'tracking' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => $host,
            'port' => env('DB_PORT_TRACKING', '3306'),
            'database' => env('DB_DATABASE_TRACKING', 'tracking'),
            'username' => env('DB_USERNAME_TRACKING', 'gold'),
            'password' => env('DB_PASSWORD_TRACKING', 'sinagjmsmis'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // For ATDs DB connection
        'hrissinag' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => $host,
            'port' => env('DB_PORT_HRISSINAG', '3306'),
            'database' => env('DB_DATABASE_HRISSINAG', 'hrissinag'),
            'username' => env('DB_USERNAME_HRISSINAG', 'gold'),
            'password' => env('DB_PASSWORD_HRISSINAG', 'sinagjmsmis'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // Accesslevel Databse
        'access' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => $host,
            'port' => env('DB_PORT_ALVL', '3306'),
            'database' => env('DB_DATABASE_ALVL', 'access'),
            'username' => env('DB_USERNAME_ALVL', 'gold'),
            'password' => env('DB_PASSWORD_ALVL', 'sinagjmsmis'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // Laravel System Configurations Database
        'laravelsysconfigs' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => $host,
            'port' => env('DB_PORT_LARAVELSYSCONFIGS', '3306'),
            'database' => env('DB_DATABASE_LARAVELSYSCONFIGS', 'laravelsysconfigs'),
            'username' => env('DB_USERNAME_LARAVELSYSCONFIGS', 'gold'),
            'password' => env('DB_PASSWORD_LARAVELSYSCONFIGS', 'sinagjmsmis'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'cssystem' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => $host,
            'port' => env('DB_PORT_ALVL', '3306'),
            'database' => env('DB_DATABASE_ALVL', 'cssystem'),
            'username' => env('DB_USERNAME_ALVL', 'gold'),
            'password' => env('DB_PASSWORD_ALVL', 'sinagjmsmis'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'sinagsms' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => '192.168.70.2',
            'port' => env('DB_PORT_ALVL', '3306'),
            'database' => env('DB_DATABASE_SMS', 'sinagsms'),
            'username' => env('DB_USERNAME_SMS', 'gold'),
            'password' => env('DB_PASSWORD_SMS', 'sinagjmsmis'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'generaldcpr' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => $host,
            'port' => env('DB_PORT_ALVL', '3306'),
            'database' => env('DB_DATABASE_DCPR', 'generaldcpr'),
            'username' => env('DB_USERNAME_DCPR', 'gold'),
            'password' => env('DB_PASSWORD_DCPR', 'sinagjmsmis'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
