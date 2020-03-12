<?php

namespace Huztw\Admin;

use InvalidArgumentException;

class AdminBuilder
{
    /**
     * 模板
     *
     */
    protected $blade = [];

    /**
     * 名稱
     *
     */
    protected $name;

    /**
     * 敘述
     *
     */
    protected $description;

    /**
     * css
     *
     */
    protected $styles = [];

    /**
     * css
     *
     */
    protected $scripts = [];

    /**
     * 名稱
     *
     */
    protected $view_path;

    /**
     * layouts
     *
     */
    protected $layouts = [];

    /**
     * 模板名稱
     *
     */
    public function build($blade, $name)
    {
        $path = $this->view_path();
        file_put_contents("$path/$name.blade.php", $blade);
        
        return $blade;
    }

    protected function view_path()
    {
        return $this->view_path ?: resource_path('views');
    }

    protected function getBlade($name)
    {
        $path = $this->view_path();

        $blade = $path . DIRECTORY_SEPARATOR . $name . '.blade.php';

        if (!file_exists($blade)) {
            throw new InvalidArgumentException("$blade is not existed");
        }

        return $blade;
    }

    /**
     * 合併
     *
     */
    public function merge(array $blades)
    {
        $merge = '';
        foreach ($blades as $blade) {
            $merge .= file_get_contents($this->getBlade($blade));

        }

        return $merge;
    }
}
