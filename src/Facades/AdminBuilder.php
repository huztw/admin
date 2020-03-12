<?php

namespace Huztw\Admin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class AdminBuilder.
 *
 * @see \Huztw\Admin\AdminBuilder
 */
class AdminBuilder extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Huztw\Admin\AdminBuilder::class;
    }
}
