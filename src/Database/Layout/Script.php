<?php

namespace Huztw\Admin\Database\Layout;

use Illuminate\Database\Eloquent\Model;

class Script extends Model
{
    protected $fillable = ['name', 'slug', 'script'];

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
        return config('admin.database.scripts_table');
    }

    /**
     * @param $script
     */
    public function setScriptAttribute($script)
    {
        $this->attributes['script'] = htmlentities($script, ENT_COMPAT, 'UTF-8');
    }

    /**
     * @param $script
     *
     * @return string
     */
    public function getScriptAttribute($script)
    {
        return html_entity_decode($script, ENT_COMPAT, 'UTF-8');
    }

    /**
     * A script belongs to many views.
     *
     * @return BelongsToMany
     */
    public function views()
    {
        $pivotTable = config('admin.database.view_scripts_table');

        $relatedModel = config('admin.database.views_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'script_id', 'view_id')->withTimestamps();
    }

    /**
     * A script belongs to many blades.
     *
     * @return BelongsToMany
     */
    public function blades()
    {
        $pivotTable = config('admin.database.blade_scripts_table');

        $relatedModel = config('admin.database.blades_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'script_id', 'blade_id')->withTimestamps();
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
