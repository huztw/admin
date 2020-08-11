<?php

namespace Huztw\Admin;

use Huztw\Admin\Traits\Bootstraps;
use Huztw\Admin\Traits\HasAssets;
use Huztw\Admin\Traits\Routes;
use Huztw\Admin\View\Content;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class Admin
{
    use Bootstraps, HasAssets, Routes;

    /**
     * The Admin version.
     *
     * @var string
     */
    const VERSION = '1.4.1';

    /**
     * The Admin path.
     *
     * @var string
     */
    private static $PATH = __DIR__ . '/../';

    /**
     * Get the Admin application path.
     *
     * @param  string|array  $path
     * @return string
     */
    public static function app_path($path = '')
    {
        if (!empty($path)) {
            $path = Arr::wrap($path);

            $path = implode(DIRECTORY_SEPARATOR, $path);
        }

        return realpath(self::$PATH . $path);
    }

    /**
     * Get the Admin config path.
     *
     * @param  string  $path
     * @return string
     */
    public static function config_path($path = '')
    {
        return self::app_path(['config', $path]);
    }

    /**
     * Get the Admin database path.
     *
     * @param  string  $path
     * @return string
     */
    public static function database_path($path = '')
    {
        return self::app_path(['database', $path]);
    }

    /**
     * Get the Admin resources path.
     *
     * @param  string  $path
     * @return string
     */
    public static function resource_path($path = '')
    {
        return self::app_path(['resources', $path]);
    }

    /**
     * Returns the long version of Huztw-admin.
     *
     * @return string The long application version
     */
    public static function getLongVersion()
    {
        return sprintf('Huztw-admin <comment>version</comment> <info>%s</info>', self::VERSION);
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        return $this->guard()->user();
    }

    /**
     * Attempt to get the guard from the local cache.
     *
     * @return \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
     */
    public function guard()
    {
        $guard = config('admin.auth.guard', 'admin');

        return Auth::guard($guard);
    }

    /**
     * @param Closure $callable
     *
     * @return \Huztw\Admin\View\Content
     */
    public function content(Closure $callable = null)
    {
        return new Content($callable);
    }
}
