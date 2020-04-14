<?php

namespace Huztw\Admin\Database\Layout;

use Illuminate\Database\Eloquent\Model;

class View extends Model
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
     * Database table.
     *
     * @return string
     */
    public static function table()
    {
        return config('admin.database.views_table');
    }

    /**
     * A view belongs to many blades.
     *
     * @return BelongsToMany
     */
    public function blades()
    {
        $pivotTable = config('admin.database.view_blades_table');

        $relatedModel = config('admin.database.blades_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'view_id', 'blade_id')
            ->withPivot(['view_id', 'blade_id', 'type', 'sort'])->withTimestamps();
    }

    /**
     * A view belongs to many assets.
     *
     * @return BelongsToMany
     */
    public function assets()
    {
        $pivotTable = config('admin.database.view_assets_table');

        $relatedModel = config('admin.database.assets_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'view_id', 'asset_id')
            ->withPivot(['view_id', 'asset_id', 'type', 'sort'])->withTimestamps();
    }

    /**
     * Get all assets for view.
     *
     * @return object
     */
    public function isLayout()
    {
        return $this->blades->filter(function ($item, $key) {
            return 'layout' == $item->pivot->type;
        });
    }

    /**
     * Get all assets for view.
     *
     * @return object
     */
    public function isNotLayout()
    {
        return $this->blades->filter(function ($item, $key) {
            return 'layout' != $item->pivot->type;
        });
    }

    /**
     * Get all assets for view.
     *
     * @return object
     */
    public function allAssets()
    {
        return $this->blades()->with('assets')->get()->pluck('assets')->flatten()->merge($this->assets);
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
            $model->blades()->detach();
            $model->assets()->detach();
        });
    }
}
