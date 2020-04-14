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
            $table->string('slug', 50)->unique();
            $table->string('name', 50)->unique();
            $table->timestamps();
        });

        Schema::create(config('admin.database.permissions_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 50)->unique();
            $table->string('name', 50)->unique();
            $table->boolean('disable')->default(false);
            $table->timestamps();
        });

        Schema::create(config('admin.database.routes_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->nullable();
            $table->string('http_method');
            $table->string('http_path');
            $table->string('visibility')->default('protected');
            $table->unique(['http_path', 'http_method'])->index();
            $table->timestamps();
        });

        Schema::create(config('admin.database.actions_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 50)->unique();
            $table->string('name', 50)->nullable();
            $table->string('visibility')->default('protected');
            $table->timestamps();
        });

        Schema::create(config('admin.database.user_permissions_table'), function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('permission_id')->unsigned();
            $table->primary(['user_id', 'permission_id'])->index();
            $table->timestamps();
        });

        Schema::create(config('admin.database.role_users_table'), function (Blueprint $table) {
            $table->integer('role_id');
            $table->integer('user_id');
            $table->primary(['role_id', 'user_id'])->index();
            $table->timestamps();
        });

        Schema::create(config('admin.database.role_permissions_table'), function (Blueprint $table) {
            $table->integer('role_id')->unsigned();
            $table->integer('permission_id')->unsigned();
            $table->primary(['role_id', 'permission_id'])->index();
            $table->timestamps();
        });

        Schema::create(config('admin.database.permission_routes_table'), function (Blueprint $table) {
            $table->integer('permission_id')->unsigned();
            $table->integer('route_id')->unsigned();
            $table->primary(['permission_id', 'route_id'])->index();
            $table->timestamps();
        });

        Schema::create(config('admin.database.permission_actions_table'), function (Blueprint $table) {
            $table->integer('permission_id')->unsigned();
            $table->integer('action_id')->unsigned();
            $table->primary(['permission_id', 'action_id'])->index();
            $table->timestamps();
        });

        Schema::create(config('admin.database.views_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 50)->unique();
            $table->string('name', 50)->nullable();
            $table->timestamps();
        });

        Schema::create(config('admin.database.blades_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 50)->unique();
            $table->string('name', 50)->nullable();
            $table->timestamps();
        });

        Schema::create(config('admin.database.assets_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 50)->unique();
            $table->string('name', 50)->nullable();
            $table->string('asset')->unique();
            $table->timestamps();
        });

        Schema::create(config('admin.database.view_blades_table'), function (Blueprint $table) {
            $table->integer('view_id')->unsigned();
            $table->integer('blade_id')->unsigned();
            $table->primary(['view_id', 'blade_id'])->index();
            $table->string('type', 50);
            $table->integer('sort');
            $table->timestamps();
        });

        Schema::create(config('admin.database.view_assets_table'), function (Blueprint $table) {
            $table->integer('view_id')->unsigned();
            $table->integer('asset_id')->unsigned();
            $table->primary(['view_id', 'asset_id'])->index();
            $table->string('type', 50);
            $table->integer('sort');
            $table->timestamps();
        });

        Schema::create(config('admin.database.blade_assets_table'), function (Blueprint $table) {
            $table->integer('blade_id')->unsigned();
            $table->integer('asset_id')->unsigned();
            $table->primary(['blade_id', 'asset_id', 'type', 'sort'])->index();
            $table->string('type', 50);
            $table->integer('sort');
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
        // Auth
        Schema::dropIfExists(config('admin.database.users_table'));
        Schema::dropIfExists(config('admin.database.roles_table'));
        Schema::dropIfExists(config('admin.database.permissions_table'));
        Schema::dropIfExists(config('admin.database.routes_table'));
        Schema::dropIfExists(config('admin.database.actions_table'));
        Schema::dropIfExists(config('admin.database.user_permissions_table'));
        Schema::dropIfExists(config('admin.database.role_users_table'));
        Schema::dropIfExists(config('admin.database.role_permissions_table'));
        Schema::dropIfExists(config('admin.database.permission_routes_table'));
        Schema::dropIfExists(config('admin.database.permission_actions_table'));

        // Layout
        Schema::dropIfExists(config('admin.database.views_table'));
        Schema::dropIfExists(config('admin.database.blades_table'));
        Schema::dropIfExists(config('admin.database.view_blades_table'));
    }
}
