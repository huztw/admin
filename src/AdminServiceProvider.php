<?php

namespace Huztw\Admin;

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
        // Console\PublishCommand::class,
        // Console\UninstallCommand::class,
        // Console\ImportCommand::class,
        // Console\CreateUserCommand::class,
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
            'admin.log',
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

        if (file_exists($routes = admin_path('routes.php'))) {
            $this->loadRoutesFrom($routes);
        }

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/admin.php', 'admin');

        // Register the service the package provides.
        $this->app->singleton('admin', function ($app) {
            return new Admin;
        });

        $this->registerRouteMiddleware();
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
            __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'admin-migrations');

        // Publishing assets.
        $this->publishes([
        __DIR__.'/../resources/assets' => public_path('vendor/huztw'),
        ], 'admin-assets');

        // Publishing the translation files.
        $this->publishes([
        __DIR__.'/../resources/lang' => resource_path('lang'),
        ], 'admin-lang');

        // Registering package commands.      
        $this->commands(self::$commands);
    }
}
