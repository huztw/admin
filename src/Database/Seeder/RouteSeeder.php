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
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $routes     = collect(BaseRoute::getRoutes());
        $routesdata = [
            [
                'http_path'   => config('admin.route.prefix') . '*',
                'http_method' => '',
                'description' => "All admin's permission",
                "created_at"  => Carbon::now(),
                "updated_at"  => Carbon::now(),
            ],
        ];

        foreach ($routes as $route) {
            $uri = $route->uri();

            if (0 === strpos($uri, '_ignition')) {
                continue;
            }

            foreach ($route->methods() as $http_method) {
                if ('HEAD' !== $http_method) {
                    array_push($routesdata, [
                        'http_path'   => $uri,
                        'http_method' => $http_method,
                        'description' => '',
                        "created_at"  => Carbon::now(),
                        "updated_at"  => Carbon::now(),
                    ]);
                }
            }
        }

        // create a route.
        Route::insertOrIgnore($routesdata);

        // reset AUTO_INCREMENT
        $increments = Route::max('id') + 1;
        DB::statement("ALTER TABLE " . config('admin.database.routes_table') . " AUTO_INCREMENT = " . $increments);

        // update route's description
        if (file_exists(config('admin.route.description'))) {
            $routeDescriptions = require config('admin.route.description');

            $routeModel = config('admin.database.routes_model');

            $routesGroup = $routeModel::all()->groupBy('http_method');

            foreach ($routesGroup as $http_method => $routes) {
                if (isset($routeDescriptions[$http_method])) {
                    foreach ($routes as $route) {
                        if (isset($routeDescriptions[$http_method][$route->http_path])) {
                            $route->description = $routeDescriptions[$http_method][$route->http_path];

                            $route->save();
                        }
                    }
                }
            }
        }
    }
}
