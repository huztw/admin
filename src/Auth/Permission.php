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
     * @var string
     */
    protected $user_slug;

    /**
     * @return void
     */
    public function __construct()
    {
        admin_error();

        $this->user = Admin::class;

        $this->user_slug = 'admin';
    }

    /**
     * Check permission.
     *
     * @param string $permission
     * @param callback|null $callback
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
     * @param int $code
     * @param callback|null $callback
     *
     * @return mixed
     */
    public function error($code, callable $callback = null)
    {
        $error = null;

        admin_error($this->httpStatusMessage($code));

        if ($callback instanceof \Closure) {
            $error = $callback();
        }

        if (!request()->pjax() && request()->ajax()) {
            abort($code, $this->httpStatusMessage($code));
        }

        return $error;
    }

    /**
     * Http response status message.
     *
     * @param $code
     *
     * @return string|null
     */
    protected function httpStatusMessage($code)
    {
        return trans("admin.http.status.$code");
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
        $this->user_slug = $user;

        $settingUser = config('admin.permission.' . $user);

        if (!class_exists($settingUser)) {
            throw new \InvalidArgumentException("Invalid permission user class [$user].");
        }

        $this->user = $settingUser;
    }
}
