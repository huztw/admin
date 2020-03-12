<?php

namespace Huztw\Admin\Traits;

use Huztw\Admin\Controllers\AuthController;

trait Routes
{
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
            $authController = config('admin.auth.controller', AuthController::class);

            /* @var \Illuminate\Routing\Router $router */
            $router->get('login', $authController . '@showLoginForm')->name('admin.login');
            $router->post('login', $authController . '@login');
            $router->get('logout', $authController . '@logout')->name('admin.logout');
            $router->post('logout', $authController . '@logout');
            $router->get('register', $authController . '@showRegistrationForm')->name('admin.register');
            $router->put('register', $authController . '@register');
        });
    }
}
