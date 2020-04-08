<?php

namespace Huztw\Admin\Database\Auth;

use Illuminate\Database\Eloquent\Model;

class Blade extends Model
{
    protected $fillable = ['name', 'slug'];

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
     * Database table of Views.
     *
     * @return string
     */
    public static function table()
    {
        return config('admin.database.blades_table');
    }

    /**
     * A blade belongs to many views.
     *
     * @return BelongsToMany
     */
    public function views()
    {
        $pivotTable = config('admin.database.view_blades_table');

        $relatedModel = config('admin.database.views_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'blade_id', 'view_id')->withTimestamps();
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
            $model->views()->detach();
        });
    }
}
