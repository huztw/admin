<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Huztw-admin name
    |--------------------------------------------------------------------------
    |
    | This value is the name of Huztw-admin, This setting is displayed on the
    | login page.
    |
     */
    'name'                      => 'Huztw-admin',

    /*
    |--------------------------------------------------------------------------
    | Huztw-admin logo
    |--------------------------------------------------------------------------
    |
    | The logo of all admin pages. You can also set it as an image by using a
    | `img` tag, eg '<img src="http://logo-url" alt="Admin logo">'.
    |
     */
    'logo'                      => '<b>Laravel</b> admin',

    /*
    |--------------------------------------------------------------------------
    | Huztw-admin mini logo
    |--------------------------------------------------------------------------
    |
    | The logo of all admin pages when the sidebar menu is collapsed. You can
    | also set it as an image by using a `img` tag, eg
    | '<img src="http://logo-url" alt="Admin logo">'.
    |
     */
    'logo-mini'                 => '<b>La</b>',

    /*
    |--------------------------------------------------------------------------
    | Huztw-admin bootstrap setting
    |--------------------------------------------------------------------------
    |
    | This value is the path of huztw-admin bootstrap file.
    |
     */
    'bootstrap'                 => app_path('Admin/bootstrap.php'),

    /*
    |--------------------------------------------------------------------------
    | Application Default
    |--------------------------------------------------------------------------
    |
    | This value is the default for application.
    |
     */

    'default'                   => 'admin',

    /*
    |--------------------------------------------------------------------------
    | Huztw-admin route settings
    |--------------------------------------------------------------------------
    |
    | The routing configuration of the admin page, including the path prefix,
    | the controller namespace, and the default middleware. If you want to
    | access through the root path, just set the prefix to empty string.
    |
     */
    'route'                     => [

        'prefix'     => env('ADMIN_ROUTE_PREFIX', 'admin'),

        'namespace'  => 'App\\Admin\\Controllers',

        'middleware' => ['web', 'admin'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Huztw-admin install directory
    |--------------------------------------------------------------------------
    |
    | The installation directory of the controller and routing configuration
    | files of the administration page. The default is `app/Admin`, which must
    | be set before running `artisan admin::install` to take effect.
    |
     */
    'directory'                 => app_path('Admin'),

    /*
    |--------------------------------------------------------------------------
    | Huztw-admin html title
    |--------------------------------------------------------------------------
    |
    | Html title for all pages.
    |
     */
    'title'                     => 'Admin',

    /*
    |--------------------------------------------------------------------------
    | Access via `https`
    |--------------------------------------------------------------------------
    |
    | If your page is going to be accessed via https, set it to `true`.
    |
     */
    'https'                     => env('ADMIN_HTTPS', false),

    /*
    |--------------------------------------------------------------------------
    | Administrator
    |--------------------------------------------------------------------------
    |
    | Which role's slug has all permission
    |
     */
    'administrator'             => 'administrator',

    /*
    |--------------------------------------------------------------------------
    | Route default value
    |--------------------------------------------------------------------------
    |
    | Set a default value for route in database.
    |
     */
    'default_routes'            => [

        // Set routes visibility by use "public", "protected", "private"
        'visibility' => 'protected',
    ],

    /*
    |--------------------------------------------------------------------------
    | Huztw-admin permission setting
    |--------------------------------------------------------------------------
    |
    | Set user authenticatable to use permission.
    |
     */
    'permission'                => [
        'admin' => Huztw\Admin\Facades\Admin::class,
        // 'user'  => Illuminate\Support\Facades\Auth::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Huztw-admin auth setting
    |--------------------------------------------------------------------------
    |
    | Authentication settings for all admin pages. Include an authentication
    | guard and a user provider setting of authentication driver.
    |
     */
    'auth'                      => [

        'controller'  => 'LoginController',

        'guard'       => 'admin',

        'guards'      => [
            'admin' => [
                'driver'   => 'session',
                'provider' => 'admin',
            ],
        ],

        'providers'   => [
            'admin' => [
                'driver' => 'eloquent',
                'model'  => Huztw\Admin\Database\Auth\Administrator::class,
            ],
        ],

        // Add "remember me" to login form
        'remember'    => true,

        // Redirect to the specified URI when user is not authorized.
        'redirect_to' => 'login',
    ],

    /*
    |--------------------------------------------------------------------------
    | Huztw-admin upload setting
    |--------------------------------------------------------------------------
    |
    | File system configuration for form upload files and images, including
    | disk and upload path.
    |
     */
    'upload'                    => [

        // Disk in `config/filesystem.php`.
        'disk'      => 'admin',

        // Image and file upload path under the disk above.
        'directory' => [
            'image' => 'images',
            'file'  => 'files',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Huztw-admin database settings
    |--------------------------------------------------------------------------
    |
    | Here are database settings for huztw-admin builtin model & tables.
    |
     */
    'database'                  => [

        // Database connection for following tables.
        'connection'               => '',

        // User tables and model.
        'users_table'              => 'admin_users',
        'users_model'              => Huztw\Admin\Database\Auth\Administrator::class,

        // Role table and model.
        'roles_table'              => 'admin_roles',
        'roles_model'              => Huztw\Admin\Database\Auth\Role::class,

        // Permission table and model.
        'permissions_table'        => 'admin_permissions',
        'permissions_model'        => Huztw\Admin\Database\Auth\Permission::class,

        // Menu table and model.
        'menu_table'               => 'admin_menu',
        'menu_model'               => Huztw\Admin\Database\Auth\Menu::class,

        // Route table and model.
        'routes_table'             => 'admin_routes',
        'routes_model'             => Huztw\Admin\Database\Auth\Route::class,

        // Action table and model.
        'actions_table'            => 'admin_actions',
        'actions_model'            => Huztw\Admin\Database\Auth\Action::class,

        // View table and model.
        'views_table'              => 'admin_views',
        'views_model'              => Huztw\Admin\Database\Layout\View::class,

        // Blade table and model.
        'blades_table'             => 'admin_blades',
        'blades_model'             => Huztw\Admin\Database\Layout\Blade::class,

        // Asset table and model.
        'assets_table'             => 'admin_assets',
        'assets_model'             => Huztw\Admin\Database\Layout\Asset::class,

        // Pivot table for table above.
        'operation_log_table'      => 'admin_operation_log',
        'user_permissions_table'   => 'admin_user_permissions',
        'role_users_table'         => 'admin_role_users',
        'role_permissions_table'   => 'admin_role_permissions',
        'role_menu_table'          => 'admin_role_menu',
        'permission_routes_table'  => 'admin_permission_routes',
        'permission_actions_table' => 'admin_permission_actions',
        'view_blades_table'        => 'admin_view_blades',
        'view_assets_table'        => 'admin_view_assets',
        'blade_assets_table'       => 'admin_blade_assets',
    ],

    /*
    |--------------------------------------------------------------------------
    | User operation log setting
    |--------------------------------------------------------------------------
    |
    | By setting this option to open or close operation log in huztw-admin.
    |
     */
    'operation_log'             => [

        'enable'          => true,

        /*
         * Only logging allowed methods in the list
         */
        'allowed_methods' => ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE', 'PATCH'],

        /*
         * Routes that will not log to database.
         *
         * All method to path like: admin/auth/logs
         * or specific method to path like: get:admin/auth/logs.
         */
        'except'          => [
            'admin/auth/logs*',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Indicates whether to check menu roles.
    |--------------------------------------------------------------------------
     */
    'check_menu_roles'          => true,

    /*
    |--------------------------------------------------------------------------
    | User default avatar
    |--------------------------------------------------------------------------
    |
    | Set a default avatar for newly created users.
    |
     */
    'default_avatar'            => '/vendor/huztw-admin/img/user-160x160.jpg',

    /*
    |--------------------------------------------------------------------------
    | Admin map field provider
    |--------------------------------------------------------------------------
    |
    | Supported: "tencent", "google", "yandex".
    |
     */
    'map_provider'              => 'google',

    /*
    |--------------------------------------------------------------------------
    | Login page background image
    |--------------------------------------------------------------------------
    |
    | This value is used to set the background image of login page.
    |
     */
    'login_background_image'    => '',

    /*
    |--------------------------------------------------------------------------
    | Show version at footer
    |--------------------------------------------------------------------------
    |
    | Whether to display the version number of huztw-admin at the footer of
    | each page
    |
     */
    'show_version'              => true,

    /*
    |--------------------------------------------------------------------------
    | Show environment at footer
    |--------------------------------------------------------------------------
    |
    | Whether to display the environment at the footer of each page
    |
     */
    'show_environment'          => true,

    /*
    |--------------------------------------------------------------------------
    | Menu bind to permission
    |--------------------------------------------------------------------------
    |
    | whether enable menu bind to a permission
     */
    'menu_bind_permission'      => true,

    /*
    |--------------------------------------------------------------------------
    | Enable default breadcrumb
    |--------------------------------------------------------------------------
    |
    | Whether enable default breadcrumb for every page content.
     */
    'enable_default_breadcrumb' => true,

    /*
    |--------------------------------------------------------------------------
    | Enable/Disable assets minify
    |--------------------------------------------------------------------------
     */
    'minify_assets'             => [

        // Assets will not be minified.
        'excepts' => [

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Enable/Disable sidebar menu search
    |--------------------------------------------------------------------------
     */
    'enable_menu_search'        => true,

    /*
    |--------------------------------------------------------------------------
    | Alert message that will displayed on top of the page.
    |--------------------------------------------------------------------------
     */
    'top_alert'                 => '',

    /*
    |--------------------------------------------------------------------------
    | The global Grid action display class.
    |--------------------------------------------------------------------------
     */
    'grid_action_class'         => \Encore\Admin\Grid\Displayers\DropdownActions::class,

    /*
    |--------------------------------------------------------------------------
    | Extension Directory
    |--------------------------------------------------------------------------
    |
    | When you use command `php artisan admin:extend` to generate extensions,
    | the extension files will be generated in this directory.
     */
    'extension_dir'             => app_path('Admin/Extensions'),

    /*
    |--------------------------------------------------------------------------
    | Settings for extensions.
    |--------------------------------------------------------------------------
    |
    | You can find all available extensions here
    | https://github.com/huztw-admin-extensions.
    |
     */
    'extensions'                => [

    ],
];
