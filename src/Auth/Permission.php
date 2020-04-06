<?php

namespace Huztw\Admin\Auth;

use Closure;
use Huztw\Admin\Database\Auth\Action;
use Huztw\Admin\Database\Auth\Permission as PermissionDB;
use Huztw\Admin\Database\Auth\Route;
use Illuminate\Http\Request;

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
    protected $items = [];

    /**
     * @var string
     */
    protected $items_slug;

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

        admin_error();
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
     * Get user's permissions.
     *
     * @return \Huztw\Admin\Auth\Permission
     */
    public function permission()
    {
        $this->items_slug = 'permission';

        if (!$this->user) {
            $this->user();
        }

        if ($this->user::user()) {
            $this->items = $this->user::user()->allPermissions()->all();
        }

        return $this;
    }

    /**
     * Get user's routes.
     *
     * @return \Huztw\Admin\Auth\Permission
     */
    public function route()
    {
        $this->items_slug = 'route';

        if (!$this->user) {
            $this->user();
        }

        if ($this->user::user()) {
            $this->items = $this->user::user()->allRoutes()->all();
        }

        return $this;
    }

    /**
     * Get user's actions.
     *
     * @return \Huztw\Admin\Auth\Permission
     */
    public function action()
    {
        $this->items_slug = 'action';

        if (!$this->user) {
            $this->user();
        }

        if ($this->user::user()) {
            $this->items = $this->user::user()->allActions()->all();
        }

        return $this;
    }

    /**
     * Check.
     *
     * @param string|array|null $args
     * @param callback|null $callback
     *
     * @return mixed
     */
    public function check($args = null, callable $callback = null)
    {
        if ($this->items_slug === null) {
            throw new \InvalidArgumentException("Invalid check with [$this->items_slug].");
        }

        $method = "shouldPassThrough" . ucfirst($this->items_slug);

        if (!method_exists($this, $method)) {
            throw new \InvalidArgumentException("The [$this->items_slug] check method does not exist.");
        }

        $args = is_array($args) ? $args : [$args];

        $check = true;

        collect($args)->each(function ($arg) use ($method, &$check) {
            if (!call_user_func([$this, $method], $arg)) {
                $check = false;
            }
        });

        if (!$check) {
            if ($callback instanceof Closure) {
                return $callback();
            }

            return false;
        }

        return true;
    }

    /**
     * Determine if the user has a permission that should pass through.
     *
     * @param string $permission
     *
     * @return bool
     */
    protected function shouldPassThroughPermission($permission): bool
    {
        if (empty($permission)) {
            return true;
        }

        // Determine if permission is disable.
        if (PermissionDB::where('slug', $permission)->get()->first(function ($permission) {return $permission->disable ?? false;})) {
            return true;
        }

        if (!$this->user::user()) {
            $this->error(401);
            return false;
        }

        if ($this->user::user()->cannot($this->items_slug, $permission)) {
            $this->error(403);
            return false;
        }

        return true;
    }

    /**
     * Determine if the user has a route that should pass through.
     *
     * @param string $permission
     *
     * @return bool
     */
    protected function shouldPassThroughRoute($request): bool
    {
        if (empty($request)) {
            return true;
        }

        if (!$request instanceof Request) {
            throw new \InvalidArgumentException('The parameter should be instance of \Illuminate\Http\Request.');
        }

        // Get route's visibility.
        if (Route::getProtectedRoute($request)) {
            $visibility = Route::getProtectedRoute($request)->visibility;
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

        if ($this->user::user()->cannot($this->items_slug, $request)) {
            $this->error(403);
            return false;
        }

        return true;
    }

    /**
     * Determine if the user has a action that should pass through.
     *
     * @param string $action
     *
     * @return bool
     */
    protected function shouldPassThroughAction($action): bool
    {
        if (empty($action)) {
            return true;
        }

        // Get action's visibility.
        if (($item = Action::where('slug', $action)->get()->first()) === null) {
            $visibility = Action::getPublic();
        } else {
            $visibility = $item->visibility;
        }

        if (Action::getPublic() == $visibility) {
            return true;
        }

        if (Action::getPrivate() == $visibility) {
            $this->error(423);
            return false;
        }

        if (!$this->user::user()) {
            $this->error(404);
            return false;
        }

        if ($this->user::user()->cannot($this->items_slug, $action)) {
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
     * @param string $user
     *
     * @return \Huztw\Admin\Auth\Permission
     */
    public function user($user = null)
    {
        $this->user_slug = $user ?? 'admin';

        $setting = config('admin.permission.' . $this->user_slug);

        if (!class_exists($setting)) {
            throw new \InvalidArgumentException("Invalid permission user class [$this->user_slug].");
        }

        $this->user = $setting;

        return $this;
    }
}
