<?php

namespace Huztw\Admin\Database\Auth;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $fillable = ['name', 'slug', 'visibility'];

    /**
     * @var array
     */
    protected static $visibility = [
        'public'    => 'public',
        'protected' => 'protected',
        'private'   => 'private',
    ];

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
        return config('admin.database.actions_table');
    }

    /**
     * A role belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function permissions()
    {
        $pivotTable = config('admin.database.permission_actions_table');

        $relatedModel = config('admin.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'action_id', 'permission_id')->withTimestamps();
    }

    /**
     * Get visibility is Public.
     *
     * @return string
     */
    public static function getPublic()
    {
        return self::$visibility['public'];
    }

    /**
     * Get visibility is protected.
     *
     * @return string
     */
    public static function getProtected()
    {
        return self::$visibility['protected'];
    }

    /**
     * Get visibility is private.
     *
     * @return string
     */
    public static function getPrivate()
    {
        return self::$visibility['private'];
    }

    /**
     * Determine if the action can pass through.
     *
     * @param string|null $slug
     *
     * @return bool
     */
    public function can($slug = null): bool
    {
        if (empty($slug)) {
            return true;
        }

        if ($slug == $this->slug) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the action can not pass.
     *
     * @param string|null $slug
     *
     * @return bool
     */
    public function cannot($slug = null): bool
    {
        return !$this->can($slug);
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
