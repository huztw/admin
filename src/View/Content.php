<?php

namespace Huztw\Admin\View;

use Closure;
use Huztw\Admin\Database\Layout\View;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;

class Content implements Renderable
{

    /**
     * @var string
     */
    protected $view;

    /**
     * @var string
     */
    protected $layout;

    /**
     * @var array
     */
    protected $contents = [];

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
     * Get Content value.
     *
     * @param string $layout
     * @param array  $data
     *
     * @return $this
     */
    public function get($name)
    {
        return $this->$name;
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
        $this->append('admin::partials.style', ['_style_' => $styles]);

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
        $this->append('admin::partials.script', ['_script_' => $scripts]);

        return $this;
    }

    /**
     * Content layout.
     *
     * @param string $layout
     * @param array  $data
     *
     * @return $this
     */
    public function layout($layout, $data = [])
    {
        $this->layout = view($layout, $data);

        return $this;
    }

    /**
     * Content view.
     *
     * @param string $view
     *
     * @return $this
     */
    public function find($view)
    {
        $get = View::where('slug', $view)->get()->first();

        if (!$get) {
            throw new \InvalidArgumentException("Invalid view [$view].");
        }

        $this->view = $get;

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
        $this->find($view);

        $blades = collect($this->view->blades->all())->pluck('slug')->toArray();

        $this->layout(array_shift($blades), $mergeData);

        foreach ($blades as $key => $blade) {
            if (isset($data[$blade])) {
                $shift = array_shift($data[$blade]);
            } else {
                $shift = Arr::first($data, function ($value, $key) use (&$data) {
                    if (is_int($key)) {
                        unset($data[$key]);
                        return true;
                    }
                });
            }

            $this->append($blade, $shift ?? []);
        }

        return $this;
    }

    /**
     * Prepend content for content body.
     *
     * @param string $view
     * @param array  $data
     *
     * @return $this
     */
    public function prepend($view, $data = [])
    {
        if (!$view instanceof Renderable) {
            $view = view($view, $data);
        }

        array_unshift($this->contents, $view);

        return $this;
    }

    /**
     * Append content for content body.
     *
     * @param string $view
     * @param array  $data
     *
     * @return $this
     */
    public function append($view, $data = [])
    {
        if (!$view instanceof Renderable) {
            $view = view($view, $data);
        }

        array_push($this->contents, $view);

        return $this;
    }

    /**
     * Render this content.
     *
     * @return string
     */
    public function render()
    {
        if ($this->layout) {
            foreach ($this->contents as $key => $content) {
                $this->layout->with($key, $content);
            }

            $this->prepend($this->layout);
        }

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
}
