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
        $routesdata = [];
        foreach ($routes as $route) {
            $uri = $route->uri();

            if (0 === strpos($uri, '_')) {
                continue;
            }

            foreach ($route->methods() as $http_method) {
                if ('HEAD' !== $http_method) {
                    array_push($routesdata, [
                        'http_path'   => $uri,
                        'http_method' => $http_method,
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
    }
}
