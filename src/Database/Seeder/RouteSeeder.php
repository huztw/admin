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
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->getRoutesDefault();

        $routeSettings = require __DIR__ . '/../../' . 'routes.description.php';

        if (!$this->isFirstRun()) {
            $userSettings  = require config('admin.route.description');
            $routeSettings = array_merge($routeSettings, $userSettings);
        }

        $this->getRoutes($routeSettings);

        $routes = $this->routes_sort($this->routes);

        // create a route.
        Route::insertOrIgnore($routes);

        // reset AUTO_INCREMENT
        $increments = Route::max('id') + 1;
        DB::statement("ALTER TABLE " . config('admin.database.routes_table') . " AUTO_INCREMENT = " . $increments);
    }

    /**
     * Get the default routes.
     *
     * @return void
     */
    protected function getRoutesDefault()
    {
        if ($this->isFirstRun()) {
            return;
        }

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
     * Get the routes's setting.
     *
     * @param $routes
     *
     * @return void
     */
    protected function getRoutes($routes)
    {
        foreach ($routes as $key => $description) {
            if (is_int($key)) {
                $key         = $description;
                $description = '';
            }

            $route                = $this->getPathMethod($key);
            $route['description'] = $description;

            if ($this->isRouteExist($route)) {
                continue;
            }

            $this->setRoutes([
                'http_path'   => $route['http_path'],
                'http_method' => $route['http_method'],
                'description' => $route['description'],
            ]);
        }
    }

    /**
     * Check if is first time.
     *
     * @return bool
     */
    protected function isFirstRun()
    {
        return !file_exists(config('admin.route.description'));
    }

    /**
     * Get the routes's path and method.
     *
     * @return array
     */
    protected function getPathMethod($str)
    {
        $array = explode(':', $str);
        if (1 == count($array)) {
            $method = '';
            $path   = array_shift($array);
        } else {
            $method = strtoupper(preg_replace('/[\n\r\t ]/', '', array_shift($array)));
            $path   = array_shift($array);
        }

        return ['http_method' => $method, 'http_path' => $path];
    }

    /**
     * Check the route for existence.
     *
     * @return bool
     */
    protected function isRouteExist($route)
    {
        $exist = false;

        foreach ($this->routes as $key => $routeDefault) {
            if ($route['http_path'] == $routeDefault['http_path']) {
                $this->changeRoute($route, $key);
                $exist = true;
            }
        }

        if ($exist && substr_count($route['http_method'], ',') > 0) {
            $this->setRoutes([
                'http_path'   => $route['http_path'],
                'http_method' => $route['http_method'],
                'description' => $route['description'],
            ]);
        }

        return $exist;
    }

    /**
     * Change the route.
     *
     * @return void
     */
    protected function changeRoute($route, $key)
    {
        if (substr_count($route['http_method'], ',') > 0) {
            $methods = explode(',', $route['http_method']);

            foreach ($methods as $method) {
                if ($method == $this->routes[$key]['http_method']) {
                    unset($this->routes[$key]);
                    break;
                }
            }
        } elseif ($route['http_method'] == $this->routes[$key]['http_method']) {
            $this->routes[$key]['description'] = $route['description'];
        }
    }

    /**
     * Set the routes.
     *
     * @return void
     */
    protected function setRoutes($route)
    {
        $http_method = isset($route['http_method']) ? $route['http_method'] : '';
        $description = isset($route['description']) ? $route['description'] : '';
        array_push($this->routes, [
            'http_path'   => $route['http_path'],
            'http_method' => $http_method,
            'description' => $description,
            "created_at"  => Carbon::now(),
            "updated_at"  => Carbon::now(),
        ]);
    }

    /**
     * Sort the routes.
     *
     * @return array
     */
    protected function routes_sort($routes)
    {
        $paths = [];

        foreach ($routes as $key => $route) {
            $paths[$key] = $route['http_path'];
        }

        array_multisort($paths, SORT_ASC, $routes);

        return $routes;
    }
}
