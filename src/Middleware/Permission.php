<?php

namespace Huztw\Admin\Middleware;

use Huztw\Admin\Auth\Permission as Checker;
use Huztw\Admin\Database\Auth\Permission as PermissionDB;
use Huztw\Admin\Database\Auth\Route;
use Huztw\Admin\Facades\Admin;
use Illuminate\Http\Request;

class Permission
{
    /**
     * @var object
     */
    protected $user;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string                   $user
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next, $user = null)
    {
        if (config('admin.check_route_permission') === false) {
            return $next($request);
        }

        $visibility = $this->getRouteVisibility($request);

        if (Route::getPrivate() == $visibility) {
            Checker::error(423);
        }

        if (Route::getPublic() == $visibility) {
            return $next($request);
        }

        $this->setUser($user);

        if (!$this->user::user()) {
            Checker::error(404);
        }

        if (!PermissionDB::userPermissions($this->user::user())->first(function ($permission) use ($request) {
            return $permission->shouldPassThrough($request);
        })) {
            Checker::error(403);
        }

        return $next($request);
    }

    /**
     * Get route's visibility.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    protected function getRouteVisibility($request)
    {
        if (Route::getProtectedRoute($request) !== null) {
            return Route::getProtectedRoute($request)->visibility;
        }

        return Route::getPublic();
    }

    /**
     * Set User.
     *
     * @param $user
     *
     * @return void
     */
    protected function setUser($user)
    {
        if (empty($user)) {
            $this->user = Admin::class;
        } else {
            $settingUser = config('admin.permission.user.' . $user);

            if (!$settingUser) {
                throw new \InvalidArgumentException("Invalid permission user class [$user].");
            }

            $this->user = $settingUser;
        }
    }
}
