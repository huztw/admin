<?php

namespace Huztw\Admin\Database\Layout;

use Illuminate\Database\Eloquent\Model;

class Style extends Model
{
    protected $fillable = ['name', 'slug', 'style'];

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
        return config('admin.database.styles_table');
    }

    /**
     * @param $style
     */
    public function setStyleAttribute($style)
    {
        $this->attributes['style'] = htmlentities($style, ENT_COMPAT, 'UTF-8');
    }

    /**
     * @param $style
     *
     * @return string
     */
    public function getStyleAttribute($style)
    {
        return html_entity_decode($style, ENT_COMPAT, 'UTF-8');
    }

    /**
     * A style belongs to many views.
     *
     * @return BelongsToMany
     */
    public function views()
    {
        $pivotTable = config('admin.database.view_styles_table');

        $relatedModel = config('admin.database.views_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'style_id', 'view_id')->withTimestamps();
    }

    /**
     * A style belongs to many blades.
     *
     * @return BelongsToMany
     */
    public function blades()
    {
        $pivotTable = config('admin.database.blade_styles_table');

        $relatedModel = config('admin.database.blades_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'style_id', 'blade_id')->withTimestamps();
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
