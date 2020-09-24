<?php

namespace Huztw\Admin\Database\Layout;

use Illuminate\Database\Eloquent\Model;

class Blade extends Model
{
    protected $fillable = ['name', 'blade'];

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
        return config('admin.database.blades_table');
    }

    /**
     * A blade belongs to many views.
     *
     * @return \Huztw\Admin\Database\Layout\View
     */
    public function views()
    {
        $pivotTable = config('admin.database.blade_views_table');

        $relatedModel = config('admin.database.views_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'blade_id', 'view_id')->withTimestamps();
    }

    /**
     * A blade belongs to many assets.
     *
     * @return \Huztw\Admin\Database\Layout\Asset
     */
    public function assets()
    {
        $pivotTable = config('admin.database.asset_blades_table');

        $relatedModel = config('admin.database.assets_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'blade_id', 'asset_id')
            ->withPivot(['blade_id', 'asset_id', 'type', 'sort'])->withTimestamps();
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
            $model->assets()->detach();
        });
    }
}
