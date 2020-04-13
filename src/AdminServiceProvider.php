<?php

namespace Huztw\Admin;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    public static $commands = [
        Console\AdminCommand::class,
        // Console\MakeCommand::class,
        // Console\MenuCommand::class,
        Console\InstallCommand::class,
        Console\PublishCommand::class,
        Console\PushCommand::class,
        Console\UninstallCommand::class,
        // Console\ImportCommand::class,
        Console\UserCommand::class,
        // Console\ResetPasswordCommand::class,
        // Console\ExtendCommand::class,
        // Console\ExportSeedCommand::class,
        // Console\MinifyCommand::class,
        // Console\FormCommand::class,
        // Console\PermissionCommand::class,
        // Console\ActionCommand::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'admin.auth'       => Middleware\Authenticate::class,
        'admin.pjax'       => Middleware\Pjax::class,
        'admin.log'        => Middleware\LogOperation::class,
        'admin.permission' => Middleware\Permission::class,
        'admin.bootstrap'  => Middleware\Bootstrap::class,
        'admin.session'    => Middleware\Session::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'admin' => [
            'admin.auth',
            'admin.pjax',
            // 'admin.log',
            'admin.bootstrap',
            'admin.permission',
            //            'admin.session',
        ],
    ];

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'admin');

        $this->ensureHttps();

        if (file_exists($routes = admin_path('routes.php'))) {
            Route::prefix(config('admin.route.prefix', 'admin'))
                ->middleware(config('admin.route.middleware', ['web', 'admin']))
                ->namespace(config('admin.route.namespace', 'App\Admin\Controllers'))
                ->group($routes);
        }

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $this->loadValidators();
    }

    /**
     * Force to set https scheme if https enabled.
     *
     * @return void
     */
    protected function ensureHttps()
    {
        if (config('admin.https') || config('admin.secure')) {
            url()->forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadAdminAuthConfig();

        $this->registerRouteMiddleware();
    }

    /**
     * Setup auth configuration.
     *
     * @return void
     */
    protected function loadAdminAuthConfig()
    {
        config(Arr::dot(config('admin.auth', []), 'auth.'));
    }

    /**
     * Register the route middleware.
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }

        // register middleware group.
        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['admin'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/' => config_path(),
        ], 'admin.config');

        // Publishing the migrations.
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'admin-migrations');

        // Publishing assets.
        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path('vendor/huztw-admin'),
        ], 'admin-assets');

        // Publishing the translation files.
        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang'),
        ], 'admin-lang');

        // Publishing the views files.
        $this->publishes([
            __DIR__ . '/../resources/views/errors' => resource_path('views/errors/admin'),
        ], 'admin-errors');

        // Publishing the views files.
        $this->publishes([
            __DIR__ . '/../resources/views/layouts' => resource_path('views/layouts'),
        ], 'admin-layouts');

        // Registering package commands.
        $this->commands(self::$commands);
    }

    /**
     * Validation booting.
     *
     * @return void
     */
    protected function loadValidators()
    {
        // The field under validation must be entirely alphabetic characters.
        Validator::extend('alpha_unicode', function ($attribute, $value, $parameters, $validator) {
            return is_string($value) && preg_match('/^[a-zA-Z]+$/u', $value);
        }, trans('admin.validation.alpha_unicode'));

        // The field under validation may have alpha-numeric characters, as well as dashes and underscores.
        Validator::extend('alpha_dash_unicode', function ($attribute, $value, $parameters, $validator) {
            return is_string($value) && preg_match('/^[a-zA-Z0-9_-]+$/u', $value);
        }, trans('admin.validation.alpha_dash_unicode'));

        // The field under validation must be entirely alpha-numeric characters.
        Validator::extend('alpha_num_unicode', function ($attribute, $value, $parameters, $validator) {
            return is_string($value) && preg_match('/^[a-zA-Z0-9]+$/u', $value);
        }, trans('admin.validation.alpha_num_unicode'));
    }
}
