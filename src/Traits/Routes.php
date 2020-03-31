<?php

namespace Huztw\Admin\Traits;

use Illuminate\Support\Facades\Route;

trait Routes
{
    /**
     * Register the laravel-admin builtin routes.
     *
     * @return void
     */
    public function routes()
    {
        $authController = config('admin.auth.controller', 'LoginController');

        Route::get('login', $authController . '@showLoginForm')->name('admin.login');
        Route::post('login', $authController . '@login');
        Route::get('logout', $authController . '@logout')->name('admin.logout');
        Route::post('logout', $authController . '@logout');
        Route::get('register', $authController . '@showRegistrationForm')->name('admin.register');
        Route::post('register', $authController . '@register');
    }
}
