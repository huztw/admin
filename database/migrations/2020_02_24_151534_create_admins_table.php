<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return config('admin.database.connection') ?: config('database.default');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('admin.database.users_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 190)->unique();
            $table->string('password', 60);
            $table->string('name');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create(config('admin.database.roles_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->string('slug', 50)->unique();
            $table->timestamps();
        });

        Schema::create(config('admin.database.permissions_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->string('slug', 50)->unique();
            $table->boolean('disable')->default(false);
            $table->timestamps();
        });

        Schema::create(config('admin.database.routes_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->nullable();
            $table->string('http_method');
            $table->string('http_path');
            $table->string('visibility')->default('protected');
            $table->index(['http_path', 'http_method']);
            $table->unique(['http_path', 'http_method']);
            $table->timestamps();
        });

        Schema::create(config('admin.database.actions_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->nullable();
            $table->string('slug', 50)->unique();
            $table->string('visibility')->default('protected');
            $table->timestamps();
        });

        Schema::create(config('admin.database.views_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->nullable();
            $table->string('slug', 50)->unique();
            $table->timestamps();
        });

        Schema::create(config('admin.database.blades_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->nullable();
            $table->string('slug', 50)->unique();
            $table->timestamps();
        });

        Schema::create(config('admin.database.user_permissions_table'), function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('permission_id');
            $table->index(['user_id', 'permission_id']);
            $table->timestamps();
        });

        Schema::create(config('admin.database.role_users_table'), function (Blueprint $table) {
            $table->integer('role_id');
            $table->integer('user_id');
            $table->index(['role_id', 'user_id']);
            $table->timestamps();
        });

        Schema::create(config('admin.database.role_permissions_table'), function (Blueprint $table) {
            $table->integer('role_id');
            $table->integer('permission_id');
            $table->index(['role_id', 'permission_id']);
            $table->timestamps();
        });

        Schema::create(config('admin.database.permission_routes_table'), function (Blueprint $table) {
            $table->integer('permission_id');
            $table->integer('route_id');
            $table->index(['permission_id', 'route_id']);
            $table->timestamps();
        });

        Schema::create(config('admin.database.permission_actions_table'), function (Blueprint $table) {
            $table->integer('permission_id');
            $table->integer('action_id');
            $table->index(['permission_id', 'action_id']);
            $table->timestamps();
        });

        Schema::create(config('admin.database.view_blades_table'), function (Blueprint $table) {
            $table->integer('view_id');
            $table->integer('blade_id');
            $table->index(['view_id', 'blade_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('admin.database.users_table'));
        Schema::dropIfExists(config('admin.database.roles_table'));
        Schema::dropIfExists(config('admin.database.permissions_table'));
        Schema::dropIfExists(config('admin.database.routes_table'));
        Schema::dropIfExists(config('admin.database.actions_table'));
        Schema::dropIfExists(config('admin.database.views_table'));
        Schema::dropIfExists(config('admin.database.blades_table'));
        Schema::dropIfExists(config('admin.database.user_permissions_table'));
        Schema::dropIfExists(config('admin.database.role_users_table'));
        Schema::dropIfExists(config('admin.database.role_permissions_table'));
        Schema::dropIfExists(config('admin.database.permission_routes_table'));
        Schema::dropIfExists(config('admin.database.permission_actions_table'));
        Schema::dropIfExists(config('admin.database.view_blades_table'));
    }
}
