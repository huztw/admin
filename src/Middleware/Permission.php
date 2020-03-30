<?php

namespace Huztw\Admin\Middleware;

use Huztw\Admin\Auth\Permission as Checker;
use Huztw\Admin\Database\Auth\Permission as PermissionDB;
use Huztw\Admin\Database\Auth\Route;
use Illuminate\Http\Request;

class Permission extends Checker
{
    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

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
        $visibility = $this->getRouteVisibility($request);

        if (Route::getPrivate() == $visibility) {
            $this->error_exit(423);
        }

        if (Route::getPublic() == $visibility) {
            return $next($request);
        }

        if ($user !== null) {
            $this->setUser($user);
        }

        if (!$this->user::user()) {
            $this->error_exit(404);
        }

        if (!PermissionDB::userPermissions($this->user::user())->first(function ($permission) use ($request) {
            return $permission->shouldPassThrough($request);
        })) {
            $this->error_exit(403);
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
     * Send error response and abort.
     *
     * @param int $status
     *
     * @return void
     */
    protected function error_exit($status)
    {
        $this->error($status, function () use ($status) {
            abort($status, $this->httpStatusMessage($status));
        });
    }
}
