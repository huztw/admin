<?php

namespace Huztw\Admin;

use Closure;
use Huztw\Admin\Database\Auth\View;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;

class Content implements Renderable
{
    /**
     * @var string
     */
    protected $layout;

    /**
     * Content title.
     *
     * @var string
     */
    protected $title = '';

    /**
     * @var array
     */
    protected $contents = [];

    /**
     * @var array
     */
    protected $_style_ = [];

    /**
     * @var array
     */
    protected $_script_ = [];

    /**
     * Content constructor.
     *
     * @param Closure|null $callback
     */
    public function __construct(\Closure $callback = null)
    {
        if ($callback instanceof Closure) {
            $callback($this);
        }
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Content style.
     *
     * @param string $styles
     *
     * @return $this
     */
    public function style(...$styles)
    {
        foreach ($styles as $style) {
            array_push($this->_style_, $style);
        }

        return $this;
    }

    /**
     * Content script.
     *
     * @param string $scripts
     *
     * @return $this
     */
    public function script(...$scripts)
    {
        foreach ($scripts as $script) {
            array_push($this->_script_, $script);
        }

        return $this;
    }

    /**
     * Content layout.
     *
     * @return string
     */
    public function layout($layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Append view content for content body.
     *
     * @param string $view
     * @param array  $data
     * @param array  $mergeData
     *
     * @return $this
     */
    public function view($view, $data = [], $mergeData = [])
    {
        $views = array_map(function ($item) use (&$data, $mergeData) {
            if (isset($data[$item])) {
                $shift = array_shift($data[$item]);
            } else {
                $shift = Arr::first($data, function ($value, $key) use (&$data) {
                    if (is_int($key)) {
                        unset($data[$key]);
                        return true;
                    }
                });
            }

            return view($item, array_merge($mergeData, $shift ?? []));
        }, $this->getBlades($view));

        $this->append(...$views);

        return $this;
    }

    /**
     * Get blades from view.
     *
     * @param $view
     *
     * @return array
     */
    protected function getBlades($view)
    {
        $blades = View::where('slug', $view)->get()->first();

        if (!$blades) {
            throw new \InvalidArgumentException("Invalid view [$view].");
        }

        return $blades->blades->pluck('slug')->toArray();
    }

    /**
     * Append content for content body.
     *
     * @param $content
     *
     * @return $this
     */
    public function append(...$contents)
    {
        foreach ($contents as $content) {
            array_push($this->contents, $content);
        }

        return $this;
    }

    /**
     * Build html of content.
     *
     * @return string
     */
    public function build()
    {
        ob_start();

        foreach ($this->contents as $content) {
            if ($content instanceof Renderable) {
                echo $content->render();
            } elseif ($content instanceof Closure) {
                $content();
            } else {
                echo (string) $content;
            }
        }

        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }

    /**
     * Render this content.
     *
     * @return string
     */
    public function render()
    {
        $items = [
            '_title_'   => $this->title,
            '_content_' => $this->build(),
            '_user_'    => [],
            '_style_'   => array_filter(array_unique($this->_style_)),
            '_script_'  => array_filter(array_unique($this->_script_)),
        ];

        if (!$this->layout) {
            $this->layout = 'admin';
        }

        return view(config('admin.layout.' . $this->layout), $items)->render();
    }
}
