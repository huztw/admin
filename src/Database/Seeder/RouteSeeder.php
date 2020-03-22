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
        'DELETE', 'GET', 'HEAD', 'OPTIONS', 'PATCH', 'POST', 'PUT',
    ];

    /**
     * @var array
     */
    protected static $exceptMethods = [
        'HEAD', 'OPTIONS',
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
            $this->pushOrigin();
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

        if (!isset($settings['routes'])) {
            return;
        }

        foreach ($settings['routes'] as $http_path => $array) {
            foreach ($array as $http_method => $name) {
                if (is_int(($http_method))) {
                    $http_method = $this->httpMethods();
                }

                if (($http_methods = $this->getHttpMethods($http_method)) === false) {
                    continue;
                }

                array_push($this->routesUser, [
                    'http_path'   => $http_path,
                    'http_method' => $http_methods,
                    'name'        => $name,
                ]);
            }
        }
    }

    /**
     * Get Http Method
     *
     * @return array
     */
    protected function httpMethods()
    {
        return array_diff(self::$httpMethods, self::$exceptMethods);
    }

    /**
     * Get Http Method array
     *
     * @param $methods
     *
     * @return mixed
     */
    protected function getHttpMethods($methods)
    {
        $methods = is_array($methods) ? $methods : explode(',', $methods);

        $httpMethods = $this->httpMethods();

        if (empty(array_filter($methods))) {
            return $httpMethods;
        } else {
            foreach ($methods as $method) {
                if (!in_array($method, $httpMethods)) {
                    return false;
                }
            }
        }

        return $methods;
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
                if (!in_array($http_method, self::$exceptMethods)) {
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
        $this->routes = array_merge($this->routesDefault, $this->compareDefaultRoutes($this->routesUser));

        $this->routes = array_filter(array_map([$this, 'formatRoute'], $this->routes));

        foreach ($this->routes as $route) {
            $searchRoute = $this->searchRoute($this->routes, $route);

            if ($searchRoute === false) {
                continue;
            }

            // If there have groups of Http Method, remove the alone
            if (count($searchRoute['big']) > 0) {
                foreach ($searchRoute['big'] as $key) {
                    unset($this->routes[$key]);
                }
            }

            // If many of the same, keep last one, remove others
            if (count($searchRoute['same']) > 1) {
                array_pop($searchRoute['same']);

                foreach ($searchRoute['same'] as $key) {
                    unset($this->routes[$key]);
                }
            }
        }
    }

    /**
     * Search the method.
     *
     * @param $routes
     * @param $search
     *
     * @return mixed
     */
    protected function searchMethod($routes, $search)
    {
        $http_path = str_replace('/', '\/', $search['http_path']);

        foreach ($routes as $key => $route) {
            if ('*' == $http_path || preg_match("/^$http_path/", $route['http_path'])) {
                $route['http_method'] = $this->getHttpMethods($route['http_method']);

                if (in_array($search['http_method'], $route['http_method'])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Search the route.
     *
     * @param $routes
     * @param $search
     *
     * @return mixed
     */
    protected function searchRoute($routes, $search)
    {
        $result = [
            'big'   => [],
            'small' => [],
            'same'  => [],
            'match' => [],
        ];

        foreach ($routes as $key => $route) {
            $http_path = str_replace('/', '\/', $search['http_path']);
            if ('*' == $http_path || preg_match("/^$http_path/", $route['http_path'])) {
                $route['http_method']  = $this->getHttpMethods($route['http_method']);
                $search['http_method'] = $this->getHttpMethods($search['http_method']);

                if ($search['http_path'] == $route['http_path']) {
                    if (count(array_intersect($search['http_method'], $route['http_method'])) > 0) {
                        if (count(array_diff($search['http_method'], $route['http_method'])) > 0) {
                            array_push($result['big'], $key);
                        } elseif (count(array_diff($route['http_method'], $search['http_method'])) > 0) {
                            array_push($result['small'], $key);
                        } else {
                            array_push($result['same'], $key);
                        }
                    }
                } else {
                    array_push($result['match'], $key);
                }
            }
        }

        if (empty(array_filter($result))) {
            return false;
        }

        return $result;
    }

    /**
     * Push change to origin routes.
     *
     * @param $route
     *
     * @return void
     */
    protected function pushOrigin()
    {
        $originRoutes = $this->originRoutes();

        foreach ($originRoutes as $id => $origin) {
            $searchRoute = $this->searchRoute($this->routes, $origin);

            if ($searchRoute === false || count($searchRoute['same']) != 1) {
                Route::destroy($id);
            } else {
                foreach ($searchRoute['same'] as $key) {
                    $route = Route::find($id);

                    $route->name = $this->routes[$key]['name'];

                    $route->save();
                }
            }
        }
    }

    /**
     * Compare to default routes.
     *
     * @param $routes
     *
     * @return array
     */
    protected function compareDefaultRoutes($routes)
    {
        if (count($this->routesDefault) > 0) {
            foreach ($routes as $key => $route) {
                $searchRoute = $this->searchRoute($this->routesDefault, $route);

                if ($searchRoute === false) {
                    unset($routes[$key]);
                } elseif (count($searchRoute['big']) > 0 || count($searchRoute['match']) > 0) {
                    foreach ($route['http_method'] as $method => $http_method) {
                        if ($this->searchMethod($this->routesDefault, ['http_path' => $route['http_path'], 'http_method' => $http_method]) === false) {
                            unset($routes[$key]['http_method'][$method]);
                        }
                    }
                }
            }
        }

        return $routes;
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
        if (is_array($route['http_method'])) {
            if (array_intersect($route['http_method'], $this->httpMethods()) == $this->httpMethods()) {
                $route['http_method'] = '';
            } else {
                $route['http_method'] = implode(',', $route['http_method']);
            }
        }

        $route['name']       = $route['name'] ?? null;
        $route['created_at'] = Carbon::now();
        $route['updated_at'] = Carbon::now();

        return $route;
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
                'name'        => $route->name,
            ];
        }

        return $routes;
    }
}
