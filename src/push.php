<?php

return [

    /*
    |--------------------------------------------------------------------------
    | routes settings
    |--------------------------------------------------------------------------
    |
    | use GET, POST, PUT, PATCH, DELETE, ''
    |
     */
    'routes'      => [
        config('admin.route.prefix') . '*'         => [
            "All admin's permission",
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
    | permissions settings
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
];
