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
            $table->id();
            $table->string('username', 190)->unique();
            $table->string('password', 60);
            $table->string('name');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create(config('admin.database.roles_table'), function (Blueprint $table) {
            $table->id();
            $table->string('role', 100)->unique();
            $table->string('name', 100)->nullable();
            $table->timestamps();
        });

        Schema::create(config('admin.database.permissions_table'), function (Blueprint $table) {
            $table->id();
            $table->string('permission', 100)->unique();
            $table->string('name', 100)->nullable();
            $table->boolean('disable')->default(false);
            $table->timestamps();
        });

        Schema::create(config('admin.database.routes_table'), function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable();
            $table->string('http_method');
            $table->string('http_path');
            $table->string('visibility')->default('protected');
            $table->unique(['http_path', 'http_method'])->index();
            $table->timestamps();
        });

        Schema::create(config('admin.database.actions_table'), function (Blueprint $table) {
            $table->id();
            $table->string('action', 100)->unique();
            $table->string('name', 100)->nullable();
            $table->string('visibility')->default('protected');
            $table->timestamps();
        });

        Schema::create(config('admin.database.user_permissions_table'), function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('permission_id');
            $table->foreign('user_id')->references('id')->on(config('admin.database.users_table'));
            $table->foreign('permission_id')->references('id')->on(config('admin.database.permissions_table'));
            $table->primary(['user_id', 'permission_id'])->index();
            $table->timestamps();
        });

        Schema::create(config('admin.database.role_users_table'), function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('role_id')->references('id')->on(config('admin.database.roles_table'));
            $table->foreign('user_id')->references('id')->on(config('admin.database.users_table'));
            $table->primary(['role_id', 'user_id'])->index();
            $table->timestamps();
        });

        Schema::create(config('admin.database.role_permissions_table'), function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            $table->foreign('role_id')->references('id')->on(config('admin.database.roles_table'));
            $table->foreign('permission_id')->references('id')->on(config('admin.database.permissions_table'));
            $table->primary(['role_id', 'permission_id'])->index();
            $table->timestamps();
        });

        Schema::create(config('admin.database.permission_routes_table'), function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('route_id');
            $table->foreign('permission_id')->references('id')->on(config('admin.database.permissions_table'));
            $table->foreign('route_id')->references('id')->on(config('admin.database.routes_table'));
            $table->primary(['permission_id', 'route_id'])->index();
            $table->timestamps();
        });

        Schema::create(config('admin.database.permission_actions_table'), function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('action_id');
            $table->foreign('permission_id')->references('id')->on(config('admin.database.permissions_table'));
            $table->foreign('action_id')->references('id')->on(config('admin.database.actions_table'));
            $table->primary(['permission_id', 'action_id'])->index();
            $table->timestamps();
        });

        Schema::create(config('admin.database.blades_table'), function (Blueprint $table) {
            $table->id();
            $table->string('blade', 50)->unique();
            $table->string('name', 50)->nullable();
            $table->timestamps();
        });

        Schema::create(config('admin.database.views_table'), function (Blueprint $table) {
            $table->id();
            $table->string('view', 50)->unique();
            $table->string('name', 50)->nullable();
            $table->unsignedBigInteger('blade_id')->nullable();
            $table->foreign('blade_id')->references('id')->on(config('admin.database.blades_table'));
            $table->timestamps();
        });

        Schema::create(config('admin.database.assets_table'), function (Blueprint $table) {
            $table->id();
            $table->string('asset')->unique();
            $table->string('name', 50)->nullable();
            $table->timestamps();
        });

        Schema::create(config('admin.database.view_blades_table'), function (Blueprint $table) {
            $table->unsignedBigInteger('view_id');
            $table->unsignedBigInteger('blade_id');
            $table->foreign('view_id')->references('id')->on(config('admin.database.views_table'));
            $table->foreign('blade_id')->references('id')->on(config('admin.database.blades_table'));
            $table->primary(['view_id', 'blade_id'])->index();
            $table->string('type', 50)->nullable();
            $table->integer('sort');
            $table->timestamps();
        });

        Schema::create(config('admin.database.view_assets_table'), function (Blueprint $table) {
            $table->unsignedBigInteger('view_id');
            $table->unsignedBigInteger('asset_id');
            $table->foreign('view_id')->references('id')->on(config('admin.database.views_table'));
            $table->foreign('asset_id')->references('id')->on(config('admin.database.assets_table'));
            $table->primary(['view_id', 'asset_id'])->index();
            $table->string('type', 50)->nullable();
            $table->integer('sort');
            $table->timestamps();
        });

        Schema::create(config('admin.database.blade_assets_table'), function (Blueprint $table) {
            $table->unsignedBigInteger('blade_id');
            $table->unsignedBigInteger('asset_id');
            $table->foreign('blade_id')->references('id')->on(config('admin.database.blades_table'));
            $table->foreign('asset_id')->references('id')->on(config('admin.database.assets_table'));
            $table->primary(['blade_id', 'asset_id', 'type', 'sort'])->index();
            $table->string('type', 50)->nullable();
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
        // Auth Relationship
        Schema::dropIfExists(config('admin.database.role_users_table'));
        Schema::dropIfExists(config('admin.database.user_permissions_table'));
        Schema::dropIfExists(config('admin.database.role_permissions_table'));
        Schema::dropIfExists(config('admin.database.permission_routes_table'));
        Schema::dropIfExists(config('admin.database.permission_actions_table'));
        // Auth
        Schema::dropIfExists(config('admin.database.users_table'));
        Schema::dropIfExists(config('admin.database.roles_table'));
        Schema::dropIfExists(config('admin.database.permissions_table'));
        Schema::dropIfExists(config('admin.database.routes_table'));
        Schema::dropIfExists(config('admin.database.actions_table'));

        // View Relationship
        Schema::dropIfExists(config('admin.database.view_blades_table'));
        Schema::dropIfExists(config('admin.database.view_assets_table'));
        Schema::dropIfExists(config('admin.database.blade_assets_table'));
        // View
        Schema::dropIfExists(config('admin.database.assets_table'));
        Schema::dropIfExists(config('admin.database.views_table'));
        Schema::dropIfExists(config('admin.database.blades_table'));
    }
}
