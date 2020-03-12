<?php

namespace Encore\Admin\Traits;

// use Huztw\Admin\Form;
// use Huztw\Admin\Grid;
// use Huztw\Admin\Tree;

/**
 * @deprecated
 */
trait AdminBuilder
{
    /**
     * @param \Closure $callback
     *
     * @return Grid
     */
    public static function grid(\Closure $callback)
    {
        // return new Grid(new static(), $callback);
        return;
    }

    /**
     * @param \Closure $callback
     *
     * @return Form
     */
    public static function form(\Closure $callback)
    {
        // return new Form(new static(), $callback);
        return;
    }

    /**
     * @param \Closure $callback
     *
     * @return Tree
     */
    public static function tree(\Closure $callback = null)
    {
        // return new Tree(new static(), $callback);
        return;
    }
}
