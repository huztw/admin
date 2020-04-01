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
     * @var array
     */
    protected $items;

    /**
     * @var string
     */
    protected $items_slug;

    /**
     * @return void
     */
    public function __construct($items = [], $items_slug = '', $user = 'admin')
    {
        $this->items = $items;

        $this->items_slug = $items_slug;

        admin_error();

        $this->user($user);
    }

    /**
     * Used by Collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get()
    {
        return collect($this->items);
    }

    /**
     * Get user's permission.
     *
     * @return mixed
     */
    public function permission()
    {
        $items = [];

        if ($this->user::user()) {
            $items = $this->user::user()->allPermissions()->all();
        }

        return new static($items, 'permission', $this->user_slug);
    }

    /**
     * Get user's route.
     *
     * @return mixed
     */
    public function route()
    {
        $items = [];

        if ($this->user::user()) {
            $items = $this->user::user()->allRoutes()->all();
        }

        return new static($items, 'route', $this->user_slug);
    }

    /**
     * Get user's action.
     *
     * @return mixed
     */
    public function action()
    {
        $items = [];

        if ($this->user::user()) {
            $items = $this->user::user()->allActions()->all();
        }

        return new static($items, 'action', $this->user_slug);
    }

    /**
     * Check.
     *
     * @param string $check
     * @param callback|null $callback
     *
     * @return mixed
     */
    public function check($check, callable $callback = null)
    {
        $method = "check" . ucfirst($this->items_slug);

        return $this->$method($check, $callback);
    }

    /**
     * Check permission.
     *
     * @param string $permission
     * @param callback|null $callback
     *
     * @return mixed
     */
    protected function checkPermission($permission, callable $callback = null)
    {
        if (is_array($permission)) {
            $collect = [];

            collect($permission)->each(function ($permission) use ($callback, &$collect) {
                $check = call_user_func([$this, 'check'], $permission, $callback);

                $collect[$permission] = $check ?? true;
            });

            return empty($collect) ? true : $collect;
        }

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

            $this->user($user);
        }

        return $permission;
    }

    /**
     * Set User.
     *
     * @param string $user
     *
     * @return \Huztw\Admin\Auth\Permission
     */
    public function user($user = 'admin')
    {
        $this->user_slug = $user;

        if ('admin' == $user) {
            $this->user = Admin::class;

            return $this;
        }

        $setting = config('admin.permission.' . $user);

        if (!class_exists($setting)) {
            throw new \InvalidArgumentException("Invalid permission user class [$user].");
        }

        $this->user = $setting;

        return $this;
    }
}
