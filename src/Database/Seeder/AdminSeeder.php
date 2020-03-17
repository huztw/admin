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
            'slug' => 'administrator',
        ]);

        // add role to user.
        Administrator::first()->roles()->save(Role::first());

        //create a permission
        Permission::truncate();
        Permission::insert([
            [
                'name' => 'All permission',
                'slug' => '*',
            ],
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
            ],
            [
                'name' => 'Login',
                'slug' => 'auth.login',
            ],
            [
                'name' => 'Register',
                'slug' => 'auth.register',
            ],
            [
                'name' => 'User setting',
                'slug' => 'auth.setting',
            ],
            [
                'name' => 'Auth management',
                'slug' => 'auth.management',
            ],
        ]);

        Role::first()->permissions()->save(Permission::first());

        Route::where('http_path', config('admin.route.prefix') . '*')->first()->permissions()->save(Permission::where('slug', '*')->first());
        Route::where('http_path', config('admin.route.prefix'))->first()->permissions()->save(Permission::where('slug', 'dashboard')->first());
        Route::where('http_path', config('admin.route.prefix') . '/login')->first()->permissions()->save(Permission::where('slug', 'auth.login')->first());
        Route::where('http_path', config('admin.route.prefix') . '/logout')->first()->permissions()->save(Permission::where('slug', 'auth.login')->first());
        Route::where('http_path', config('admin.route.prefix') . '/register')->first()->permissions()->save(Permission::where('slug', 'auth.register')->first());
    }
}
