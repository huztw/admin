<?php

namespace Huztw\Admin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Permission.
 *
 * @method static mixed check($permission, callable $callback = null)
 * @method static void error($status, callable $callback = null)
 * @method static bool isDisable($permission)
 *
 * @see \Huztw\Admin\Auth\Permission
 */
class Permission extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Huztw\Admin\Auth\Permission::class;
    }
}
