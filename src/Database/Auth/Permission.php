<?php

namespace Huztw\Admin\Database\Auth;

use Illuminate\Database\Eloquent\Model;

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
     * Database table.
     *
     * @return string
     */
    public static function table()
    {
        return config('admin.database.permissions_table');
    }

    /**
     * A permission belongs to many roles.
     *
     * @return \Huztw\Admin\Database\Auth\Role
     */
    public function roles()
    {
        $pivotTable = config('admin.database.role_permissions_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'permission_id', 'role_id')->withTimestamps();
    }

    /**
     * A permission belongs to many routes.
     *
     * @return \Huztw\Admin\Database\Auth\Route
     */
    public function routes()
    {
        $pivotTable = config('admin.database.permission_routes_table');

        $relatedModel = config('admin.database.routes_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'permission_id', 'route_id')->withTimestamps();
    }

    /**
     * A permission belongs to many actions.
     *
     * @return \Huztw\Admin\Database\Auth\Action
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
     * Determine if the permission can pass through.
     *
     * @param string|null $slug
     *
     * @return bool
     */
    public function can($slug = null): bool
    {
        if (empty($slug)) {
            return true;
        }

        if ($slug == $this->slug) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the permission can not pass.
     *
     * @param string|null $slug
     *
     * @return bool
     */
    public function cannot($slug = null): bool
    {
        return !$this->can($slug);
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
}
