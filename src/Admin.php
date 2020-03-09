<?php

namespace Huztw\Admin;

use Huztw\Admin\Controllers\LoginController;
use Illuminate\Support\Facades\Auth;

class Admin
{
    /**
     * The Laravel admin version.
     *
     * @var string
     */
    const VERSION = '1.0.0';

    /**
     * Returns the long version of Huztw-admin.
     *
     * @return string The long application version
     */
    public static function getLongVersion()
    {
        return sprintf('Huztw-admin <comment>version</comment> <info>%s</info>', self::VERSION);
    }

    /**
     * Register the laravel-admin builtin routes.
     *
     * @return void
     */
    public function routes()
    {
        $attributes = [
            'prefix'     => config('admin.route.prefix'),
            'middleware' => config('admin.route.middleware'),
        ];

        app('router')->group($attributes, function ($router) {
            $authController = config('admin.auth.controller', LoginController::class);

            /* @var \Illuminate\Routing\Router $router */
            $router->get('login', $authController . '@showLoginForm')->name('admin.login');
            $router->post('login', $authController . '@login');
            $router->get('logout', $authController . '@logout')->name('admin.logout');
            $router->get('register', $authController . '@showRegistrationForm')->name('admin.register');
            $router->put('register', $authController . '@register');
        });
    }

    /**
     * Attempt to get the guard from the local cache.
     *
     * @return \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
     */
    public function guard()
    {
        $guard = config('admin.auth.guard', 'admin');

        return Auth::guard($guard);
    }
}
