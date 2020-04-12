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
        // 'slug' => 'name',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions settings
    |--------------------------------------------------------------------------
    |
     */
    'permissions' => [
        '*'               => 'All permission',
        'dashboard'       => 'Dashboard',
        'auth.login'      => 'Login',
        'auth.register'   => 'Register',
        'auth.setting'    => 'User setting',
        'auth.management' => 'Auth management',
    ],

    /*
    |--------------------------------------------------------------------------
    | views settings
    |--------------------------------------------------------------------------
    |
     */
    'views'       => [
        // 'slug' => 'name',
    ],

    /*
    |--------------------------------------------------------------------------
    | blades settings
    |--------------------------------------------------------------------------
    |
     */
    'blades'      => [
        'admin::index'    => 'Admin Home',
        'admin::login'    => 'Admin Login',
        'admin::register' => 'Admin Register',
    ],

    /*
    |--------------------------------------------------------------------------
    | style settings
    |--------------------------------------------------------------------------
    |
     */
    'styles'      => [
        // 'slug' => [
        //     'name' => 'style',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | script settings
    |--------------------------------------------------------------------------
    |
     */
    'scripts'     => [
        // 'slug' => [
        //     'name' => 'script',
        // ],
    ],
];
