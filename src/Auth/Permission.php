<?php

namespace Huztw\Admin\Auth;

use Huztw\Admin\Database\Auth\Permission as Checker;
use Huztw\Admin\Facades\Admin;

class Permission
{
    /**
     * @var object
     */
    protected $user;

    /**
     * @var array
     */
    protected static $httpStatus = [
        401 => 'admin.http.status.401',
        403 => 'admin.http.status.403',
        404 => 'admin.http.status.404',
        423 => 'admin.http.status.423',
    ];

    /**
     * @return void
     */
    public function __construct()
    {
        admin_error();

        $this->user = Admin::class;
    }

    /**
     * Check permission.
     *
     * @param string $permission
     * @param callback $callback
     *
     * @return mixed
     */
    public function check($permission, callable $callback = null)
    {
        if (is_array($permission)) {
            $collect = [];

            collect($permission)->each(function ($permission) use ($callback, &$collect) {
                $check = call_user_func([$this, 'check'], $permission, $callback);

                $collect[$permission] = $check ?? true;
            });

            return empty($collect) ? true : $collect;
        }

        $permission = $this->getPermission($permission);

        if ($this->isDisable($permission)) {
            return true;
        }

        if (!$this->user::user()) {
            return $this->error(401, $callback);
        }

        if ($this->user::user()->cannot($permission)) {
            return $this->error(403, $callback);
        }
    }

    /**
     * Send error response page.
     *
     * @param int $status
     * @param callback $callback
     *
     * @return mixed
     */
    public function error($status, callable $callback = null)
    {
        $error = null;

        admin_error($this->httpStatusMessage($status));

        if ($callback instanceof \Closure) {
            $error = $callback();
        }

        if (!request()->pjax() && request()->ajax()) {
            abort($status, $this->httpStatusMessage($status));
        }

        return $error;
    }

    /**
     * Http response status message.
     *
     * @param $status
     *
     * @return string|null
     */
    protected function httpStatusMessage($status)
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
    public function isDisable($permission): bool
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
    protected function getPermission($permission)
    {
        $array = explode(':', $permission);

        if (count($array) > 1) {
            $permission = array_shift($array);

            $user = array_shift($array);

            $this->setUser($user);
        }

        return $permission;
    }

    /**
     * Set User.
     *
     * @param string $user
     *
     * @return void
     */
    protected function setUser($user)
    {
        $settingUser = config('admin.permission.' . $user);

        if (!class_exists($settingUser)) {
            throw new \InvalidArgumentException("Invalid permission user class [$user].");
        }

        $this->user = $settingUser;
    }
}
