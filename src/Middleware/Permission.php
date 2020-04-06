<?php

namespace Huztw\Admin\Middleware;

use Huztw\Admin\Auth\Permission as Checker;
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
        $check = $this->user($user)->route()->check($request, function () {
            return $this->error_exit(admin_messages('error')->first('title'));
        });

        if (admin_messages('error')) {
            return $check;
        }

        return $next($request);
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
        if (!request()->ajax()) {
            if (404 == $code) {
                $exception = new NotFoundHttpException($this->httpStatusMessage($code));
            } else {
                $exception = new HttpException($code, $this->httpStatusMessage($code));
            }

            $view = 'errors.' . $this->user_slug . '.' . $code;

            if (View::exists($view)) {
                return response()->view($view, ['exception' => $exception], $code);
            }

            abort($code, $this->httpStatusMessage($code));
        }
    }
}
