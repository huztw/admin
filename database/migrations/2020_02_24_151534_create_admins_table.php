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

        Schema::create(config('admin.database.permission_users_table'), function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained(config('admin.database.permissions_table'));
            $table->foreignId('user_id')->constrained(config('admin.database.users_table'));
            $table->primary(['permission_id', 'user_id'])->index();
            $table->timestamps();
        });

        Schema::create(config('admin.database.role_users_table'), function (Blueprint $table) {
            $table->foreignId('role_id')->constrained(config('admin.database.roles_table'));
            $table->foreignId('user_id')->constrained(config('admin.database.users_table'));
            $table->primary(['role_id', 'user_id'])->index();
            $table->timestamps();
        });

        Schema::create(config('admin.database.permission_roles_table'), function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained(config('admin.database.permissions_table'));
            $table->foreignId('role_id')->constrained(config('admin.database.roles_table'));
            $table->primary(['permission_id', 'role_id'])->index();
            $table->timestamps();
        });

        Schema::create(config('admin.database.permission_routes_table'), function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained(config('admin.database.permissions_table'));
            $table->foreignId('route_id')->constrained(config('admin.database.routes_table'));
            $table->primary(['permission_id', 'route_id'])->index();
            $table->timestamps();
        });

        Schema::create(config('admin.database.action_permissions_table'), function (Blueprint $table) {
            $table->foreignId('action_id')->constrained(config('admin.database.actions_table'));
            $table->foreignId('permission_id')->constrained(config('admin.database.permissions_table'));
            $table->primary(['action_id', 'permission_id'])->index();
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
            $table->foreignId('blade_id')->constrained(config('admin.database.blades_table'));
            $table->timestamps();
        });

        Schema::create(config('admin.database.assets_table'), function (Blueprint $table) {
            $table->id();
            $table->string('asset')->unique();
            $table->string('name', 50)->nullable();
            $table->timestamps();
        });

        Schema::create(config('admin.database.blade_views_table'), function (Blueprint $table) {
            $table->foreignId('blade_id')->constrained(config('admin.database.blades_table'));
            $table->foreignId('view_id')->constrained(config('admin.database.views_table'));
            $table->primary(['blade_id', 'view_id'])->index();
            $table->string('type', 50)->nullable();
            $table->integer('sort');
            $table->timestamps();
        });

        Schema::create(config('admin.database.asset_views_table'), function (Blueprint $table) {
            $table->foreignId('asset_id')->constrained(config('admin.database.assets_table'));
            $table->foreignId('view_id')->constrained(config('admin.database.views_table'));
            $table->primary(['asset_id', 'view_id'])->index();
            $table->string('type', 50)->nullable();
            $table->integer('sort');
            $table->timestamps();
        });

        Schema::create(config('admin.database.asset_blades_table'), function (Blueprint $table) {
            $table->foreignId('asset_id')->constrained(config('admin.database.assets_table'));
            $table->foreignId('blade_id')->constrained(config('admin.database.blades_table'));
            $table->primary(['asset_id', 'blade_id', 'type', 'sort'])->index();
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
        Schema::dropIfExists(config('admin.database.permission_users_table'));
        Schema::dropIfExists(config('admin.database.permission_roles_table'));
        Schema::dropIfExists(config('admin.database.permission_routes_table'));
        Schema::dropIfExists(config('admin.database.action_permissions_table'));
        // Auth
        Schema::dropIfExists(config('admin.database.users_table'));
        Schema::dropIfExists(config('admin.database.roles_table'));
        Schema::dropIfExists(config('admin.database.permissions_table'));
        Schema::dropIfExists(config('admin.database.routes_table'));
        Schema::dropIfExists(config('admin.database.actions_table'));

        // View Relationship
        Schema::dropIfExists(config('admin.database.blade_views_table'));
        Schema::dropIfExists(config('admin.database.asset_views_table'));
        Schema::dropIfExists(config('admin.database.asset_blades_table'));
        // View
        Schema::dropIfExists(config('admin.database.assets_table'));
        Schema::dropIfExists(config('admin.database.views_table'));
        Schema::dropIfExists(config('admin.database.blades_table'));
    }
}
