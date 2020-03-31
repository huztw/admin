<?php

namespace Huztw\Admin\Middleware;

use Huztw\Admin\Auth\Permission as Checker;
use Huztw\Admin\Database\Auth\Permission as PermissionDB;
use Huztw\Admin\Database\Auth\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            return $this->error_exit(423);
        }

        if (Route::getPublic() == $visibility) {
            return $next($request);
        }

        if ($user !== null) {
            $this->setUser($user);
        }

        if (!$this->user::user()) {
            return $this->error_exit(404);
        }

        if (!PermissionDB::userPermissions($this->user::user())->first(function ($permission) use ($request) {
            return $permission->shouldPassThrough($request);
        })) {
            return $this->error_exit(403);
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
     * Throw an HttpException with the given data.
     *
     * @param int $code
     *
     * @return mixed
     */
    protected function error_exit($code)
    {
        return $this->error($code, function () use ($code) {
            if (!request()->ajax()) {
                if (404 == $code) {
                    $exception = new NotFoundHttpException($this->httpStatusMessage($code));
                } else {
                    $exception = new HttpException($code, $this->httpStatusMessage($code));
                }

                if ($this->user_slug == 'admin') {
                    $view = 'admin::errors.' . $code;
                } else {
                    $view = 'errors.' . $this->user_slug . '.' . $code;
                }

                if (View::exists($view)) {
                    return response()->view($view, ['exception' => $exception], $code);
                }

                abort($code, $this->httpStatusMessage($code));
            }
        });
    }
}
