<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Route's description
    |--------------------------------------------------------------------------
    |
    | GET, POST, PUT, PATCH, DELETE, ''
    |
     */
    config('admin.route.prefix') . '*'                       => "All admin's permission",
    'GET:' . config('admin.route.prefix')                    => "Admin's Home Page",
    'GET,POST:' . config('admin.route.prefix') . '/login'    => 'Login to Admin',
    'GET,POST:' . config('admin.route.prefix') . '/logout'   => 'Logout from Admin',
    'GET,POST:' . config('admin.route.prefix') . '/register' => 'Register to Admin',
];
