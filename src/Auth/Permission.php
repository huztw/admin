<?php

namespace Huztw\Admin\Auth;

use Huztw\Admin\Database\Auth\Permission as Checker;
use Huztw\Admin\Facades\Admin;
use Huztw\Admin\Middleware\Pjax;

class Permission
{
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

        if (static::isDisable($permission)) {
            return true;
        }

        if (!Admin::user()) {
            static::error(401);
        }

        if (Admin::user()->cannot($permission)) {
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
        $response = response(Admin::content()->withError(self::httpStatusMessage($status)));

        if (!request()->pjax() && request()->ajax()) {
            abort($status, self::httpStatusMessage($status));
        }
        abort($status, self::httpStatusMessage($status));

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
}
