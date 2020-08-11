<?php

namespace Huztw\Admin\Database\Seeds;

use Carbon\Carbon;
use Huztw\Admin\Database\Auth\Route;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RouteSeeder extends Seeder
{
    /**
     * @var array
     */
    protected $routes = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $routes = $this->getAdmin();

        if ($this->notByInstall()) {
            $defaultRoutes = $this->getDefault();

            $userRoutes = $this->intersectRoutes(array_merge($routes, $this->getUser()), $defaultRoutes);

            $routes = array_merge($defaultRoutes, $userRoutes);
        }

        $this->settings($routes);

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
     * Get admin's routes by settings file.
     *
     * @return array
     */
    protected function getAdmin()
    {
        $routes = $this->getUser(__DIR__ . '/../../');

        $except = [
            'admin/login',
            'admin/logout',
        ];

        $routes = array_map(function ($route) use ($except) {
            if (!in_array($route['http_path'], $except)) {
                $route['visibility'] = $this->visibility('protected');
            } else {
                $route['visibility'] = $this->visibility('public');
            }

            return $route;
        }, $routes);

        return $routes;
    }

    /**
     * Set routes by settings file.
     *
     * @param $directory
     *
     * @return array
     */
    protected function getUser($directory = null)
    {
        $routes = [];

        $settings = require $this->settingsFile($directory);

        if (!isset($settings['routes'])) {
            return $routes;
        }

        foreach ($settings['routes'] as $http_path => $array) {
            foreach ($array as $http_method => $name) {
                if (is_int(($http_method))) {
                    $http_method = $this->httpMethods();
                }

                if (($http_methods = $this->getHttpMethods($http_method)) === false) {
                    continue;
                }

                array_push($routes, [
                    'http_path'   => $http_path,
                    'http_method' => $http_methods,
                    'name'        => $name,
                ]);
            }
        }

        return $routes;
    }

    /**
     * Get route visibility
     *
     * @param $setting
     *
     * @return string
     */
    protected function visibility($setting)
    {
        $visibility = Route::getPublic();

        if ('private' == $setting) {
            $visibility = Route::getPrivate();
        } elseif ('protected' == $setting) {
            $visibility = Route::getProtected();
        }

        return $visibility;
    }

    /**
     * Get Http Method
     *
     * @return array
     */
    protected function httpMethods()
    {
        return array_diff(Route::$httpMethods, Route::$exceptMethods);
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
     * Get the already exist routes.
     *
     * @return array
     */
    protected function getDefault()
    {
        $routes = [];

        foreach (collect(Route::getRoutes()) as $route) {
            $uri = $route->uri();

            // except
            if (0 === strpos($uri, '_ignition')) {
                continue;
            }

            foreach ($route->methods() as $http_method) {
                if (!in_array($http_method, Route::$exceptMethods)) {
                    array_push($routes, [
                        'http_path'   => $uri,
                        'http_method' => $http_method,
                    ]);
                }
            }
        }

        return $routes;
    }

    /**
     * Settings routes.
     *
     * @param $routes
     *
     * @return void
     */
    protected function settings($routes)
    {
        $this->routes = array_filter(array_map([$this, 'formatRoute'], $routes));

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
     * Get intersect routes.
     *
     * @param $routes
     * @param $compare
     *
     * @return array
     */
    protected function intersectRoutes($routes, $compare)
    {
        if (count($compare) > 0) {
            foreach ($routes as $key => $route) {
                $searchRoute = $this->searchRoute($compare, $route);

                if ($searchRoute === false) {
                    unset($routes[$key]);
                } elseif (count($searchRoute['big']) > 0 || count($searchRoute['match']) > 0) {
                    foreach ($route['http_method'] as $method => $http_method) {
                        if ($this->searchMethod($compare, ['http_path' => $route['http_path'], 'http_method' => $http_method]) === false) {
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
        $route['visibility'] = $route['visibility'] ?? $this->visibility(config('admin.default_routes.visibility'));
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
