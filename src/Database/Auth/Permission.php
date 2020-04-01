<?php

namespace Huztw\Admin\Database\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Permission extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'slug', 'permission', 'disable'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(self::table());

        parent::__construct($attributes);
    }

    /**
     * Database table of Permissions.
     *
     * @return string
     */
    public static function table()
    {
        return config('admin.database.permissions_table');
    }

    /**
     * Permission belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles()
    {
        $pivotTable = config('admin.database.role_permissions_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'permission_id', 'role_id')->withTimestamps();
    }

    /**
     * Permission belongs to many routes.
     *
     * @return BelongsToMany
     */
    public function routes()
    {
        $pivotTable = config('admin.database.permission_routes_table');

        $relatedModel = config('admin.database.routes_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'permission_id', 'route_id')->withTimestamps();
    }

    /**
     * Permission belongs to many actions.
     *
     * @return BelongsToMany
     */
    public function actions()
    {
        $pivotTable = config('admin.database.permission_actions_table');

        $relatedModel = config('admin.database.actions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'permission_id', 'action_id')->withTimestamps();
    }

    /**
     * @param $method
     */
    public function setHttpMethodAttribute($method)
    {
        if (is_array($method)) {
            $this->attributes['http_method'] = implode(',', $method);
        }
    }

    /**
     * @param $method
     *
     * @return array
     */
    public function getHttpMethodAttribute($method)
    {
        if (is_string($method)) {
            return array_filter(explode(',', $method));
        }

        return $method;
    }

    /**
     * @param $get
     *
     * @return bool
     */
    public function getPermissionAttribute($get)
    {
        return boolval($get);
    }

    /**
     * @param $get
     *
     * @return bool
     */
    public function getDisableAttribute($get)
    {
        return boolval($get);
    }

    /**
     * If request should pass through the current permission.
     *
     * @param Request $request
     *
     * @return true|null
     */
    public function shouldPassThrough(Request $request)
    {
        return $this->routes->first(function ($route) use ($request) {
            if ($route::isHttpPath($request, $route->http_path)) {
                if (empty($route->http_method)) {
                    return true;
                }

                foreach ($route->http_method as $http_method) {
                    if ($request->isMethod($http_method)) {
                        return true;
                    }
                }
            }
        });
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->roles()->detach();
            $model->routes()->detach();
            $model->actions()->detach();
        });
    }

    /**
     * Get all permissions of user.
     *
     * @param $user
     *
     * @return mixed
     */
    public static function userPermissions($user)
    {
        if (!method_exists($user, 'permissions')) {
            return collect([]);
        }

        if (method_exists($user, 'roles')) {
            $collect = $user->roles()->with('permissions')->get()->pluck('permissions')->flatten()->merge($user->permissions);
        } else {
            $collect = $user->permissions;
        }

        return $collect->map(function ($item, $key) {
            $item->routes = self::find($item->id)->routes;

            return $item;
        });
    }
}
