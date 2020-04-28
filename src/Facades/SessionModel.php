<?php

namespace Huztw\Admin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class SessionModel.
 *
 * @see \Huztw\Admin\Extensions\SessionModel
 */
class SessionModel extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Huztw\Admin\Extensions\SessionModel::class;
    }
}
