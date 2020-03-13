<?php

namespace Huztw\Admin\Database\Auth;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = ['http_path', 'description'];

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
     * A Route belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles()
    {
        $pivotTable = config('admin.database.role_menu_table');

        $relatedModel = config('admin.database.routes_table');

        return $this->belongsToMany($relatedModel, $pivotTable, 'route_id', 'role_id');
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
        });
    }
}
