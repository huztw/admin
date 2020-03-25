<?php

namespace Huztw\Admin\Database\Seeder;

use Huztw\Admin\Database\Auth\Administrator;
use Huztw\Admin\Database\Auth\Permission;
use Huztw\Admin\Database\Auth\Role;
use Huztw\Admin\Database\Auth\Route;
use Illuminate\Database\Seeder;
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
        Administrator::truncate();
        Administrator::create([
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'name'     => 'Administrator',
        ]);

        // create a role.
        Role::truncate();
        Role::create([
            'name' => 'Administrator',
            'slug' => config('admin.administrator'),
        ]);

        // add role to user.
        Administrator::first()->roles()->save(Role::first());

        // add permission to role.
        Role::first()->permissions()->save(Permission::first());

        // add route to permission.
        Permission::where('slug', '*')->first()->routes()->save(Route::where('http_path', config('admin.route.prefix') . '*')->first());
        Permission::where('slug', 'dashboard')->first()->routes()->save(Route::where('http_path', config('admin.route.prefix'))->first());
        Permission::where('slug', 'auth.login')->first()->routes()->save(Route::where('http_path', config('admin.route.prefix') . '/login')->first());
        Permission::where('slug', 'auth.login')->first()->routes()->save(Route::where('http_path', config('admin.route.prefix') . '/logout')->first());
        Permission::where('slug', 'auth.register')->first()->routes()->save(Route::where('http_path', config('admin.route.prefix') . '/register')->first());
    }
}
