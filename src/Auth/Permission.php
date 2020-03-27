<?php

namespace Huztw\Admin\Auth;

use Huztw\Admin\Database\Auth\Permission as Checker;
use Huztw\Admin\Facades\Admin;
use Huztw\Admin\Middleware\Pjax;

class Permission
{
    /**
     * @var object
     */
    protected static $user;

    /**
     * @var array
     */
    protected static $httpStatus = [
        401 => 'admin.deny',
        403 => 'admin.deny',
        423 => 'admin.deny',
    ];

    /**
     * Check permission.
     *
     * @param $permission
     *
     * @return true
     */
    public static function check($permission)
    {
        if (is_array($permission)) {
            collect($permission)->each(function ($permission) {
                call_user_func([self::class, 'check'], $permission);
            });

            return true;
        }

        $permission = self::getPermission($permission);

        if (static::isDisable($permission)) {
            return true;
        }

        if (!self::$user::user()) {
            static::error(401);
        }

        if (self::$user::user()->cannot($permission)) {
            static::error(403);
        }
    }

    /**
     * Send error response page.
     *
     * @param $status
     */
    public static function error($status)
    {
        abort($status, self::httpStatusMessage($status));
        $response = response(self::$user::content()->withError(self::httpStatusMessage($status)));

        if (!request()->pjax() && request()->ajax()) {
            abort($status, self::httpStatusMessage($status));
        }

        Pjax::respond($response);
    }

    /**
     * Http response status message.
     *
     * @param $status
     *
     * @return string|null
     */
    protected static function httpStatusMessage($status)
    {
        if (isset(self::$httpStatus[$status])) {
            return trans(self::$httpStatus[$status]);
        }

        return null;
    }

    /**
     * If permission is disable.
     *
     * @param $permission
     *
     * @return bool
     */
    public static function isDisable($permission): bool
    {
        return Checker::where('slug', $permission)->first()->disable;
    }

    /**
     * Get Permission.
     *
     * @param $permission
     *
     * @return string
     */
    protected static function getPermission($permission)
    {
        $array = explode(':', $permission);

        if (count($array) > 1) {
            $permission = array_shift($array);

            $user = array_shift($array);

            $settingUser = config('admin.permission.user.' . $user);

            if (!$settingUser) {
                throw new \InvalidArgumentException("Invalid permission user class [$user].");
            }

            self::$user = $settingUser;
        } else {
            self::$user = Admin::class;
        }

        return $permission;
    }
}
