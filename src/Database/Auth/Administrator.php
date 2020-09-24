<?php

namespace Huztw\Admin\Database\Auth;

use Huztw\Admin\Auth\Authorizable;

/**
 * Class Administrator.
 */
class Administrator extends Authorizable
{
    /**
     * @var array
     */
    protected $fillable = ['username', 'password', 'name'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.users_table'));

        parent::__construct($attributes);
    }

    /**
     * A user belongs to many roles.
     *
     * @return \Huztw\Admin\Database\Auth\Role
     */
    public function roles()
    {
        $pivotTable = config('admin.database.role_users_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'role_id')->withTimestamps();
    }

    /**
     * A user belongs to many permissions.
     *
     * @return \Huztw\Admin\Database\Auth\Permission
     */
    public function permissions()
    {
        $pivotTable = config('admin.database.permission_users_table');

        $relatedModel = config('admin.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'permission_id')->withTimestamps();
    }

    /**
     * Get avatar attribute.
     *
     * @param string $avatar
     *
     * @return string
     */
    public function getAvatarAttribute($avatar)
    {
        if (url()->isValidUrl($avatar)) {
            return $avatar;
        }

        $disk = config('admin.upload.disk');

        if ($avatar && array_key_exists($disk, config('filesystems.disks'))) {
            return Storage::disk(config('admin.upload.disk'))->url($avatar);
        }

        $default = config('admin.default_avatar') ?: '/vendor/huztw-admin/img/user-160x160.jpg';

        return admin_asset($default);
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function bootHasPermissions()
    {
        static::deleting(function ($model) {
            $model->roles()->detach();
            $model->permissions()->detach();
        });
    }
}
