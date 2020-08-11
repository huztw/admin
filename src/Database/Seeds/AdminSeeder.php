<?php

namespace Huztw\Admin\Database\Seeds;

use Huztw\Admin\Database\Auth\Administrator;
use Huztw\Admin\Database\Auth\Permission;
use Huztw\Admin\Database\Auth\Role;
use Huztw\Admin\Database\Auth\Route;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            RouteSeeder::class,
        ]);

        // create a user.
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Administrator::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Administrator::create([
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'name'     => 'Administrator',
        ]);

        // create a role.
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Role::create([
            'role' => config('admin.administrator'),
            'name' => config('admin.administrator'),
        ]);

        // add role to user.
        Administrator::first()->roles()->save(Role::where('role', config('admin.administrator'))->first());

        // add permission to role.
        Role::first()->permissions()->save(Permission::first());

        // add route to permission.
        Permission::where('permission', '*')->first()->routes()->save(Route::where('http_path', config('admin.route.prefix') . '*')->first());
        Permission::where('permission', 'dashboard')->first()->routes()->save(Route::where('http_path', config('admin.route.prefix'))->first());
        Permission::where('permission', 'auth.login')->first()->routes()->save(Route::where('http_path', config('admin.route.prefix') . '/login')->first());
        Permission::where('permission', 'auth.login')->first()->routes()->save(Route::where('http_path', config('admin.route.prefix') . '/logout')->first());
        Permission::where('permission', 'auth.register')->first()->routes()->save(Route::where('http_path', config('admin.route.prefix') . '/register')->first());
    }
}
