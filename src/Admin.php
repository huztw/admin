<?php

namespace Huztw\Admin;

use Huztw\Admin\Traits\Bootstraps;
use Huztw\Admin\Traits\HasAssets;
use Huztw\Admin\Traits\Routes;
use Huztw\Admin\View\Content;
use Illuminate\Support\Facades\Auth;

class Admin
{
    use Bootstraps, HasAssets, Routes;

    /**
     * The Laravel admin version.
     *
     * @var string
     */
    const VERSION = '1.3.1';

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
