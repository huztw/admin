<?php

namespace Huztw\Admin\Database\Layout;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = ['name', 'slug', 'asset'];

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
        return config('admin.database.assets_table');
    }

    /**
     * A asset belongs to many views.
     *
     * @return \Huztw\Admin\Database\Layout\View
     */
    public function views()
    {
        $pivotTable = config('admin.database.view_assets_table');

        $relatedModel = config('admin.database.views_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'asset_id', 'view_id')->withTimestamps();
    }

    /**
     * A asset belongs to many blades.
     *
     * @return \Huztw\Admin\Database\Layout\Blade
     */
    public function blades()
    {
        $pivotTable = config('admin.database.blade_assets_table');

        $relatedModel = config('admin.database.blades_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'asset_id', 'blade_id')->withTimestamps();
    }

    /**
     * @param $asset
     */
    public function setAssetAttribute($asset)
    {
        $this->attributes['asset'] = htmlentities($asset, ENT_COMPAT, 'UTF-8');
    }

    /**
     * @param $asset
     *
     * @return string
     */
    public function getAssetAttribute($asset)
    {
        return html_entity_decode($asset, ENT_COMPAT, 'UTF-8');
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
            $model->blades()->detach();
        });
    }
}
