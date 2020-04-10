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
     * Database table of Views.
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

        return $this->belongsToMany($relatedModel, $pivotTable, 'view_id', 'blade_id')->withTimestamps();
    }

    /**
     * A view belongs to many styles.
     *
     * @return BelongsToMany
     */
    public function styles()
    {
        $pivotTable = config('admin.database.view_styles_table');

        $relatedModel = config('admin.database.styles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'view_id', 'style_id')->withTimestamps();
    }

    /**
     * A view belongs to many scripts.
     *
     * @return BelongsToMany
     */
    public function scripts()
    {
        $pivotTable = config('admin.database.view_scripts_table');

        $relatedModel = config('admin.database.scripts_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'view_id', 'script_id')->withTimestamps();
    }

    /**
     * Get all styles for view.
     *
     * @return object
     */
    public function allStyles()
    {
        return $this->blades()->with('styles')->get()->pluck('styles')->flatten()->merge($this->styles);
    }

    /**
     * Get all scripts for view.
     *
     * @return object
     */
    public function allScripts()
    {
        return $this->blades()->with('scripts')->get()->pluck('scripts')->flatten()->merge($this->scripts);
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
        });
    }
}
