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
    protected $routesDefault = [];

    /**
     * @var array
     */
    protected $routesUser = [];

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
        $this->setUser(__DIR__ . '/../../');

        if ($this->notByInstall()) {
            $this->setDefault();

            $this->setUser();
        }

        $this->settings();

        // insert to database.
        Route::insertOrIgnore($this->getRoutes());

        // reset AUTO_INCREMENT
        $increments = Route::max('id') + 1;
        DB::statement("ALTER TABLE " . config('admin.database.routes_table') . " AUTO_INCREMENT = " . $increments);

        if ($this->notByInstall()) {
            $this->removeUseless();
        }
    }

    /**
     * Get the settings.
     *
     * @param $directory
     *
     * @return void
     */
    protected function setUser($directory = null)
    {
        $settings = require $this->settingsFile($directory);

        if (isset($settings['routes'])) {
            foreach ($settings['routes'] as $http_path => $array) {
                foreach ($array as $http_method => $name) {
                    if (is_int(($http_method))) {
                        $http_method = '';
                    }

                    array_push($this->routesUser, [
                        'http_path'   => $http_path,
                        'http_method' => $http_method,
                        'name'        => $name,
                    ]);
                }
            }
        }
    }

    /**
     * Isn't Run by install?
     *
     * @return bool
     */
    protected function notByInstall()
    {
        return file_exists($this->settingsFile());
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
     * Set the already exist routes.
     *
     * @return void
     */
    protected function setDefault()
    {
        foreach (collect(BaseRoute::getRoutes()) as $route) {
            $uri = $route->uri();

            // except
            if (0 === strpos($uri, '_ignition')) {
                continue;
            }

            foreach ($route->methods() as $http_method) {
                if ('HEAD' !== $http_method) {
                    array_push($this->routesDefault, [
                        'http_path'   => $uri,
                        'http_method' => $http_method,
                    ]);
                }
            }
        }
    }

    /**
     * Settings routes.
     *
     * @return void
     */
    protected function settings()
    {
        $this->routes = $this->routesDefault;

        foreach ($this->routesUser as $route) {
            if ($this->notByInstall()) {
                $this->removeNotInDefault($route);
            }

            array_push($this->routes, $route);
        }

        $this->routes = array_filter(array_map([$this, 'formatRoute'], $this->routes));
    }

    /**
     * Remove the route which one not in default.
     *
     * @param $route
     *
     * @return void
     */
    protected function removeNotInDefault($route)
    {
        $http_methods = explode(',', $route['http_method']);

        foreach ($this->routes as $key => $default) {
            if ($route['http_path'] == $default['http_path'] && in_array($default['http_method'], $http_methods)) {
                unset($this->routes[$key]);
            }
        }
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
     * Format the route.
     *
     * @param $route
     *
     * @return array
     */
    protected function formatRoute($route)
    {
        $http_method = trim($route['http_method']);

        if ($this->correctHttpMethod($http_method)) {
            $route['http_method'] = $http_method;
            $route['name']        = $route['name'] ?? null;
            $route['created_at']  = Carbon::now();
            $route['updated_at']  = Carbon::now();

            return $route;
        }

        return null;
    }

    /**
     * Is correct Http Method?
     *
     * @return bool
     */
    protected function correctHttpMethod($methods)
    {
        if (!empty($methods)) {
            foreach (explode(',', $methods) as $method) {
                if (!in_array($method, self::$httpMethods)) {
                    return false;
                }
            }
        }

        return true;
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

    /**
     * Get the original routes.
     *
     * @return array
     */
    protected function originRoutes()
    {
        $routes = [];

        foreach (Route::get() as $route) {
            $routes[$route->id] = [
                'http_path'   => $route->http_path,
                'http_method' => $route->http_method,
            ];
        }

        return $routes;
    }

    /**
     * Remove the useless routes.
     *
     * @return void
     */
    protected function removeUseless()
    {
        $defaults = [];

        foreach ($this->routesDefault as $default) {
            if (!isset($defaults[$default['http_path']])) {
                $defaults[$default['http_path']] = [$default['http_method']];
            } else {
                array_push($defaults[$default['http_path']], $default['http_method']);
            }
        }

        foreach ($this->originRoutes() as $id => $origin) {
            if (!$this->matchHttpPaths($origin['http_path'], array_keys($defaults))) {
                Route::destroy($id);
                continue;
            }

            if (isset($defaults[$origin['http_path']])) {
                $defaultsMethods = $defaults[$origin['http_path']];
            } else {
                $defaultsMethods = [];
                foreach ($defaults as $defaultPath => $defaultMethods) {
                    if ($this->matchHttpPath($origin['http_path'], $defaultPath)) {
                        foreach ($defaultMethods as $defaultMethod) {
                            if (!in_array($defaultMethod, $defaultsMethods)) {
                                array_push($defaultsMethods, $defaultMethod);
                            }
                        }
                    }
                }
            }

            $http_methods = $origin['http_method'];

            foreach ($origin['http_method'] as $key => $http_method) {
                if (!in_array($http_method, $defaultsMethods)) {
                    unset($http_methods[$key]);
                }
            }

            if (count($http_methods) != count($origin['http_method'])) {
                if (count($http_methods) == 0) {
                    Route::destroy($id);
                } else {
                    Route::where('id', $id)->update(['http_method' => implode(',', $http_methods)]);
                }
            }
        }
    }

    /**
     * match.
     *
     * @return void
     */
    protected function matchHttpPaths($path, $array)
    {
        foreach ($array as $value) {
            if ($this->matchHttpPath($path, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * match.
     *
     * @return void
     */
    protected function matchHttpPath($path, $value)
    {
        if ('*' == $path) {
            return true;
        }
        $path = str_replace('/', '\/', $path);

        return preg_match("/$path/", $value);
    }
}
