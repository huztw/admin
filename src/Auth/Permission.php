<?php

namespace Huztw\Admin\Auth;

use Closure;
use Huztw\Admin\Database\Auth\Action;
use Huztw\Admin\Database\Auth\Permission as PermissionDB;
use Huztw\Admin\Database\Auth\Route;
use Illuminate\Support\Str;

class Permission
{
    /**
     * @var object
     */
    protected $user;

    /**
     * @var string
     */
    protected $group;

    /**
     * @var string
     */
    protected $checker;

    /**
     * Permission constructor.
     *
     * @param Closure|null $callback
     */
    public function __construct(Closure $callback = null)
    {
        if ($callback instanceof Closure) {
            $callback($this);
        }

        $this->user();

        admin_error();
    }

    /**
     * @param string $method
     * @param mixed $arguments
     *
     * @return $this
     */
    public function __call($method, $arguments)
    {
        $this->checker = $method;

        return $this;
    }

    /**
     * Used by Collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get()
    {
        if ($user = $this->user::user()) {
            $all = 'all' . ucfirst(Str::plural($this->checker));
            return $user->$all()->all();
        }

        return collect([]);
    }

    /**
     * Determine if the check should pass through.
     *
     * @param string|array|null $args
     * @param callback|null $callback
     *
     * @return mixed
     */
    public function check($args = null, callable $callback = null)
    {
        if ($this->checker === null) {
            throw new \InvalidArgumentException("Invalid check with [$this->checker].");
        }

        $method = "shouldPassThrough" . ucfirst($this->checker);

        if (!method_exists($this, $method)) {
            throw new \InvalidArgumentException("The [$this->checker] check method does not exist.");
        }

        $args = !empty($args) ? (is_array($args) ? $args : [$args]) : [null];

        $reject = collect($args)->reject(function ($arg) use ($method) {
            return call_user_func([$this, $method], $arg);
        })->count();

        if ($reject == 0) {
            return true;
        }

        return ($callback instanceof Closure) ? $callback() : false;
    }

    /**
     * Determine if the user has a permission that should pass through.
     *
     * @param string $check
     *
     * @return bool
     */
    protected function shouldPassThroughPermission($check): bool
    {
        // Determine if permission is disable.
        $disable = PermissionDB::where('permission', $check)->get()->first(function ($item) {
            return $item->disable ?? false;
        });
        if ($disable) {
            return true;
        }

        if (!$this->user::user()) {
            $this->error(401);
            return false;
        }

        if ($this->user::user()->cannot($this->checker, $check)) {
            $this->error(403);
            return false;
        }

        return true;
    }

    /**
     * Determine if the user has a route that should pass through.
     *
     * @param string $check
     *
     * @return bool
     */
    protected function shouldPassThroughRoute($check): bool
    {
        // Get route's visibility.
        if ($similar = Route::similarRoute()) {
            $visibility = $similar->visibility;
        } else {
            $visibility = Route::getPublic();
        }

        if (Route::getPublic() == $visibility) {
            return true;
        }

        if (Route::getPrivate() == $visibility) {
            $this->error(423);
            return false;
        }

        if (!$this->user::user()) {
            $this->error(404);
            return false;
        }

        if ($this->user::user()->cannot($this->checker, $check)) {
            $this->error(403);
            return false;
        }

        return true;
    }

    /**
     * Determine if the user has a action that should pass through.
     *
     * @param string $check
     *
     * @return bool
     */
    protected function shouldPassThroughAction($check): bool
    {
        // Get action's visibility.
        if (($action = Action::where('action', '=', $check)->first()) === null) {
            $action = Action::create([
                'action' => $check,
                'name'   => $check,
            ]);
        }
        $visibility = $action->visibility;

        if (Action::getPublic() == $visibility) {
            return true;
        }

        if (Action::getPrivate() == $visibility) {
            $this->error(423);
            return false;
        }

        if (!$this->user::user()) {
            $this->error(401);
            return false;
        }

        if ($this->user::user()->cannot($this->checker, $check)) {
            $this->error(403);
            return false;
        }

        return true;
    }

    /**
     * Send error response page.
     *
     * @param int $code
     *
     * @return void
     */
    public function error($code)
    {
        admin_error($code, $this->httpStatusMessage($code));

        if (!request()->pjax() && request()->ajax()) {
            abort($code, $this->httpStatusMessage($code));
        }
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
     * Set User.
     *
     * @param string|null $user
     *
     * @return \Huztw\Admin\Auth\Permission
     */
    public function user($user = null)
    {
        $this->group = $user ?? config('admin.group');

        $setting = config('admin.permission.' . $this->group);

        if (!class_exists($setting)) {
            throw new \InvalidArgumentException("Invalid permission user class [$this->group].");
        }

        $this->user = $setting;

        return $this;
    }
}
