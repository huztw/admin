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

        if (!isset($settings['routes'])) {
            return;
        }

        foreach ($settings['routes'] as $http_path => $array) {
            foreach ($array as $http_method => $name) {
                if (is_int(($http_method))) {
                    $http_method = '';
                }

                $http_method = array_map(function ($m) {
                    return trim($m);
                }, explode(',', $http_method));

                if ($this->isHttpMethod($http_method) === false) {
                    continue;
                }

                array_push($this->routesUser, [
                    'http_path'   => $http_path,
                    'http_method' => $http_method,
                    'name'        => $name,
                ]);
            }
        }
    }

    /**
     * Is Http Method?
     *
     * @return bool
     */
    protected function isHttpMethod($http_method)
    {
        if (is_array($http_method)) {
            foreach ($http_method as $method) {
                if (!in_array($method, self::$httpMethods) && !empty($method)) {
                    return false;
                }
            }
        } elseif (!in_array($http_method, self::$httpMethods) && !empty($method)) {
            return false;
        }

        return true;
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
     * Search the route.
     *
     * @param $routes
     * @param $search
     *
     * @return mixed
     */
    protected function searchRoute($routes, $search)
    {
        $big   = [];
        $small = [];
        $same  = [];

        foreach ($routes as $key => $route) {
            if ($route['http_path'] == $search['http_path']) {
                $route['http_method']  = is_array($route['http_method']) ? $route['http_method'] : explode(',', $route['http_method']);
                $search['http_method'] = is_array($search['http_method']) ? $search['http_method'] : explode(',', $search['http_method']);

                if (count(array_intersect($search['http_method'], $route['http_method'])) > 0) {
                    if (count(array_diff($search['http_method'], $route['http_method'])) > 0) {
                        array_push($big, $key);
                    } elseif (count(array_diff($route['http_method'], $search['http_method'])) > 0) {
                        array_push($small, $key);
                    } else {
                        array_push($same, $key);
                    }
                } elseif (count($search['http_method']) == 0 && count($route['http_method']) == 0) {
                    array_push($same, $key);
                }
            }
        }

        if (count($big) != 0 || count($small) != 0 || count($same) != 0) {
            return ['big' => $big, 'small' => $small, 'same' => $same];
        }

        return false;
    }

    /**
     * Remove useless routes.
     *
     * @param $route
     *
     * @return void
     */
    protected function removeUseless()
    {
        $originRoutes = $this->originRoutes();

        foreach ($originRoutes as $id => $origin) {
            $searchRoute = $this->searchRoute($this->routes, $origin);
            if ($searchRoute === false || count($searchRoute['small']) > 0 || count($searchRoute['big']) > 0) {
                Route::destroy($id);
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
                } elseif (count($searchRoute['big']) > 0) {
                    foreach ($route['http_method'] as $method => $http_method) {
                        if ($this->searchRoute($this->routesDefault, ['http_path' => $route['http_path'], 'http_method' => $http_method]) === false) {
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
        $route['http_method'] = is_array($route['http_method']) ? implode(',', $route['http_method']) : $route['http_method'];
        $route['name']        = $route['name'] ?? null;
        $route['created_at']  = Carbon::now();
        $route['updated_at']  = Carbon::now();

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
