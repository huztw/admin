<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Routes settings
    |--------------------------------------------------------------------------
    |
    | use GET, POST, PUT, PATCH, DELETE, ''
    |
     */
    'routes'      => [
        config('admin.route.prefix') . '*'         => [
            'GET,POST' => "All admin's permission",
        ],
        config('admin.route.prefix')               => [
            'GET' => "Admin's Home Page",
        ],
        config('admin.route.prefix') . '/login'    => [
            'GET,POST' => 'Login to Admin',
        ],
        config('admin.route.prefix') . '/logout'   => [
            'GET,POST' => 'Logout from Admin',
        ],
        config('admin.route.prefix') . '/register' => [
            'GET,POST' => 'Register to Admin',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Actions settings
    |--------------------------------------------------------------------------
    |
     */
    'actions'     => [
        // 'name',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions settings
    |--------------------------------------------------------------------------
    |
     */
    'permissions' => [
        '*',
        'dashboard',
        'auth.login',
        'auth.register',
        'auth.setting',
        'auth.management',
    ],

    /*
    |--------------------------------------------------------------------------
    | views settings
    |--------------------------------------------------------------------------
    |
     */
    'views'       => [
        // 'name' => 'view',
    ],

    /*
    |--------------------------------------------------------------------------
    | blades settings
    |--------------------------------------------------------------------------
    |
     */
    'blades'      => [
        'Admin Home'     => 'admin::index',
        'Admin Login'    => 'admin::login',
        'Admin Register' => 'admin::register',
    ],

    /*
    |--------------------------------------------------------------------------
    | assets settings
    |--------------------------------------------------------------------------
    |
     */
    'assets'      => [
        // 'name' => 'asset',
    ],
];
