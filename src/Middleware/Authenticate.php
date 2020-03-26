<?php

namespace Huztw\Admin\Middleware;

use Closure;
use Huztw\Admin\Facades\Admin;

class Authenticate
{
    /**
     * @var array
     */
    protected $excepts = [];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Admin::guard()->guest() && !$this->isExcept($request)) {
            return $this->redirectTo($request);
        }

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        $redirectTo = admin_base_path(config('admin.auth.redirect_to', 'login'));

        return redirect()->guest($redirectTo);
    }

    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function isExcept($request): bool
    {
        $admin_login = trim(admin_base_path(config('admin.auth.redirect_to', 'login')), '/');

        array_push($this->excepts, $admin_login);

        foreach ($this->excepts as $except) {
            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
