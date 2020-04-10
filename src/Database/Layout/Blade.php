<?php

namespace Huztw\Admin\Database\Layout;

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
     * A blade belongs to many styles.
     *
     * @return BelongsToMany
     */
    public function styles()
    {
        $pivotTable = config('admin.database.blade_styles_table');

        $relatedModel = config('admin.database.styles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'blade_id', 'style_id')->withTimestamps();
    }

    /**
     * A blade belongs to many scripts.
     *
     * @return BelongsToMany
     */
    public function scripts()
    {
        $pivotTable = config('admin.database.blade_scripts_table');

        $relatedModel = config('admin.database.scripts_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'blade_id', 'script_id')->withTimestamps();
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
