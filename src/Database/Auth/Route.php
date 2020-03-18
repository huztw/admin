<?php

namespace Huztw\Admin\Database\Auth;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = ['http_path', 'http_method', 'description'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.routes_table'));

        parent::__construct($attributes);
    }

    /**
     * Route belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function permissions()
    {
        $pivotTable = config('admin.database.permission_routes_table');

        $relatedModel = config('admin.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'route_id', 'permission_id')->withTimestamps();
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
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->permissions()->detach();
        });
    }
}
