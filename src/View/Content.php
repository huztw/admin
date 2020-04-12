<?php

namespace Huztw\Admin\View;

use Closure;
use Huztw\Admin\Database\Layout\Script;
use Huztw\Admin\Database\Layout\Style;
use Huztw\Admin\Database\Layout\View;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;

class Content implements Renderable
{
    /**
     * @var string
     */
    protected $layout;

    /**
     * @var string
     */
    protected $view;

    /**
     * @var array
     */
    protected $blade = [];

    /**
     * @var array
     */
    protected $style = [];

    /**
     * @var array
     */
    protected $script = [];

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

        $this->layout();
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
    public function style($style)
    {
        if (is_array($style)) {
            collect($style)->each(function ($style) {
                call_user_func([$this, 'style'], $style);
            });

            return $this;
        }

        if ($style instanceof Style) {
            $style = $style->style;
        }

        array_push($this->_style_, $style);

        return $this;
    }

    /**
     * Content script.
     *
     * @param string $scripts
     *
     * @return $this
     */
    public function script($script)
    {
        if (is_array($script)) {
            collect($script)->each(function ($script) {
                call_user_func([$this, 'script'], $script);
            });

            return $this;
        }

        if ($script instanceof Script) {
            $script = $script->script;
        }

        array_push($this->_script_, $script);

        return $this;
    }

    /**
     * Content layout.
     *
     * @return string|null
     */
    public function layout($layout = null)
    {
        $layout = $layout ?? config('admin.default');

        $setting = config('admin.layout.' . $layout);

        if ($setting === null) {
            throw new \InvalidArgumentException("Invalid layout with [$layout].");
        }

        $this->layout = $setting;

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

        $this->allBlades();

        $this->allStyles();

        $this->allScripts();

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
        }, collect($this->blade)->pluck('slug')->toArray());

        $this->append(...$views);

        return $this;
    }

    /**
     * Get view.
     *
     * @param string $view
     *
     * @return object
     */
    public function getView($view)
    {
        $get = View::where('slug', $view)->get()->first();

        if (!$get) {
            throw new \InvalidArgumentException("Invalid view [$view].");
        }

        return $get;
    }

    /**
     * All blades from view.
     *
     * @return object
     */
    public function allBlades()
    {
        if (!$this->view) {
            throw new \InvalidArgumentException("Invalid view [$this->view].");
        }

        $this->blade = $this->view->blades->all();

        return $this;
    }

    /**
     * All styles from view.
     *
     * @return array
     */
    public function allStyles()
    {
        if (!$this->view) {
            throw new \InvalidArgumentException("Invalid view [$this->view].");
        }

        $this->style = $this->view->allStyles()->all();

        return $this;
    }

    /**
     * All scripts from view.
     *
     * @return array
     */
    public function allScripts()
    {
        if (!$this->view) {
            throw new \InvalidArgumentException("Invalid view [$this->view].");
        }

        $this->script = $this->view->allScripts()->all();

        return $this;
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

    public function getStyle()
    {

    }

    /**
     * Render this content.
     *
     * @return string
     */
    public function render()
    {
        $this->style($this->style);

        $this->script($this->script);

        $items = [
            '_title_'   => $this->title,
            '_content_' => $this->build(),
            '_user_'    => [],
            '_style_'   => array_filter(array_unique($this->_style_)),
            '_script_'  => array_filter(array_unique($this->_script_)),
        ];

        return view($this->layout, $items)->render();
    }
}
