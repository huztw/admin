<?php

namespace Huztw\Admin\Auth;

use Illuminate\Foundation\Auth\User;

/**
 * Abstract Class Authorizable.
 */
abstract class Authorizable extends User
{
    /**
     * Determine if the roles method exists.
     *
     * @return BelongsToMany
     */
    private function permissionsExists()
    {
        return method_exists($this, 'permissions');
    }

    /**
     * Determine if the roles method exists.
     *
     * @return BelongsToMany
     */
    private function rolesExists()
    {
        return method_exists($this, 'roles');
    }

    /**
     * Get user's roles.
     *
     * @return object
     */
    private function getRoles()
    {
        if ($this->rolesExists()) {
            return $this->roles;
        }

        return collect([]);
    }

    /**
     *  Get roles model.
     *
     * @return string
     */
    protected function rolesModel()
    {
        return config('admin.database.roles_model');
    }

    /**
     *  Get permissions model.
     *
     * @return string
     */
    protected function permissionsModel()
    {
        return config('admin.database.permissions_model');
    }

    /**
     * Get all permissions for user.
     *
     * @param bool $filter
     *
     * @return object
     */
    public function allPermissions($force = false)
    {
        if (!$this->permissionsExists()) {
            return collect([]);
        }

        if ($this->rolesExists()) {
            $permissions = $this->roles()->with('permissions')->get()->pluck('permissions')->flatten()->merge($this->permissions);
        } else {
            $permissions = $this->permissions;
        }

        if (!$force) {
            // Filter the permission which has disable
            $permissions = $permissions->filter(function ($item, $key) {
                return !$item->disable;
            });
        }

        return $permissions->map(function ($item, $key) {
            $item->routes  = $item->routes;
            $item->actions = $item->actions;

            return $item;
        });
    }

    /**
     * Get all routes for user.
     *
     * @param bool $filter
     *
     * @return object
     */
    public function allRoutes($force = false)
    {
        $permissions = $this->allPermissions($force);

        return $permissions->map(function ($item, $key) {
            return $item->routes;
        })->collapse();
    }

    /**
     * Get all actions for user.
     *
     * @param bool $filter
     *
     * @return object
     */
    public function allActions($force = false)
    {
        $permissions = $this->allPermissions($force);

        return $permissions->map(function ($item, $key) {
            return $item->actions;
        })->collapse();
    }

    /**
     *  Determine if the user has permission.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     *
     * @return true|null
     */
    public function can($ability, $arguments = [])
    {
        if (empty($ability)) {
            return true;
        }

        if ($this->isAdministrator()) {
            return true;
        }

        if ('permission' == $ability) {
            return $this->allPermissions()->first(function ($permission) use ($arguments) {
                return $permission->can($arguments);
            });
        } elseif ('route' == $ability) {
            return $this->allRoutes()->first(function ($route) use ($arguments) {
                return $route->can($arguments);
            });
        } elseif ('action' == $ability) {
            return $this->allActions()->first(function ($action) use ($arguments) {
                return $action->can($arguments);
            });
        }

        return false;
    }

    /**
     * Determine if the user does not have permission.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     *
     * @return bool
     */
    public function cannot($ability, $arguments = [])
    {
        return !$this->can($ability, $arguments);
    }

    /**
     * Determine if user is administrator.
     *
     * @return bool
     */
    public function isAdministrator(): bool
    {
        $administrator = config('admin.administrator', 'administrator');

        return $this->isRole($administrator);
    }

    /**
     * Determine if user is the role.
     *
     * @param string $role
     *
     * @return bool
     */
    public function isRole(string $role): bool
    {
        return $this->getRoles()->pluck('slug')->contains($role);
    }

    /**
     * Determine if user has the role.
     *
     * @param array $roles
     *
     * @return bool
     */
    public function inRoles(array $roles = []): bool
    {
        return $this->getRoles()->pluck('slug')->intersect($roles)->isNotEmpty();
    }

    /**
     * If visible for roles.
     *
     * @param $roles
     *
     * @return bool
     */
    public function visible(array $roles = []): bool
    {
        if (empty($roles)) {
            return true;
        }

        $roles = array_column($roles, 'slug');

        return $this->inRoles($roles) || $this->isAdministrator();
    }
}
