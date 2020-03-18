<?php

namespace Huztw\Admin\Database\Seeder;

use Carbon\Carbon;
use Huztw\Admin\Database\Auth\Route;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route as BaseRoute;

class RouteSeeder extends Seeder
{
    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @var array
     */
    protected static $httpMethods = [
        'GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS', 'HEAD',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = $this->getSettings(__DIR__ . '/../../');

        // Check if isn't first time run
        if (file_exists($this->settingsFile())) {
            $this->settingsByAuto();

            $settings = array_merge_recursive($settings, $this->getSettings());
        }

        $this->settings($settings);

        // insert to database.
        Route::insertOrIgnore($this->getRoutes());

        // reset AUTO_INCREMENT
        $increments = Route::max('id') + 1;
        DB::statement("ALTER TABLE " . config('admin.database.routes_table') . " AUTO_INCREMENT = " . $increments);
    }

    /**
     * Get the settings.
     *
     * @param $directory
     *
     * @return array
     */
    protected function getSettings($directory = null): array
    {
        $settings = require $this->settingsFile($directory);

        return $settings['routes'] ?? [];
    }

    /**
     * Get the settings file.
     *
     * @param $directory
     *
     * @return string
     */
    protected function settingsFile($directory = null)
    {
        $directory = $directory ?? trim(config('admin.directory'), '/');

        $file = $directory . '/push.php';

        return str_replace('/', DIRECTORY_SEPARATOR, $file);
    }

    /**
     * Set the already exist routes settings automatically.
     *
     * @return void
     */
    protected function settingsByAuto()
    {
        $routesCollect = collect(BaseRoute::getRoutes());

        foreach ($routesCollect as $route) {
            $uri = $route->uri();

            if (0 === strpos($uri, '_ignition')) {
                continue;
            }

            foreach ($route->methods() as $http_method) {
                if ('HEAD' !== $http_method) {
                    $this->setRoutes([
                        'http_path'   => $uri,
                        'http_method' => $http_method,
                    ]);
                }
            }
        }
    }

    /**
     * Get the routes settings.
     *
     * @param $routes
     *
     * @return void
     */
    protected function settings($routes)
    {
        foreach ($routes as $path => $method_name) {
            foreach ($method_name as $method => $name) {
                if (is_int($method)) {
                    $method = '';
                }

                $route = [
                    'http_path'   => $path,
                    'http_method' => $method,
                    'name'        => $name,
                ];

                if ($this->overwriteRoute($route)) {
                    continue;
                }

                $this->setRoutes($route);
            }
        }
    }

    /**
     * If the route for existence, then overwrite it.
     *
     * @return bool
     */
    protected function overwriteRoute($route)
    {
        $exist = false;

        foreach ($this->routes as $key => $finished) {
            if ($route['http_path'] == $finished['http_path']) {
                if (is_array(($methods = $this->httpMethodFormat($route['http_method'], true)))) {
                    if ($this->isRepeatRoute($methods, $finished['http_method'])) {
                        unset($this->routes[$key]);
                    }
                } elseif ($route['http_method'] == $finished['http_method']) {
                    $this->routes[$key]['name'] = $route['name'];

                    $exist = true;
                }
            }
        }

        return $exist;
    }

    /**
     * Check if is the repeat route.
     *
     * @param $methods
     * @param $finishedmethod
     *
     * @return bool
     */
    protected function isRepeatRoute($methods, $finishedmethod)
    {
        foreach ($methods as $method) {
            if ($method == $finishedmethod) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set the routes.
     *
     * @param $route
     *
     * @return void
     */
    protected function setRoutes($route)
    {
        if (($http_method = $this->httpMethodFormat($route['http_method'])) === false) {
            return;
        }

        array_push($this->routes, [
            'http_path'   => $route['http_path'],
            'http_method' => $http_method,
            'name'        => $route['name'] ?? null,
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);
    }

    /**
     * Format the Http Method.
     *
     * @param $str
     * @param $array
     *
     * @return mixed
     */
    protected function httpMethodFormat($str, $array = false)
    {
        $http_methods = [];

        foreach (explode(',', $str) as $http_method) {
            if (in_array(trim($http_method), self::$httpMethods) || empty($http_method)) {
                array_push($http_methods, $http_method);
            } else {
                return false;
            }
        }

        if ($array === false) {
            return implode(',', $http_methods);
        }

        return count($http_methods) > 1 ? $http_methods : implode(',', $http_methods);
    }

    /**
     * Get the routes.
     *
     * @return array
     */
    protected function getRoutes()
    {
        $routes = $this->routes;

        // Sort the routes
        $paths = [];

        foreach ($routes as $key => $route) {
            $paths[$key] = $route['http_path'];
        }

        array_multisort($paths, SORT_ASC, $routes);

        return $routes;
    }
}
