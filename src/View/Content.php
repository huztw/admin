<?php

namespace Huztw\Admin\View;

use Closure;
use Huztw\Admin\Database\Layout\Asset;
use Huztw\Admin\Database\Layout\Blade;
use Huztw\Admin\Database\Layout\View;
use Illuminate\Contracts\Support\Renderable;

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
     * @var array
     */
    protected $datas = [];

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
     * @param string $name
     *
     * @return mixed
     */
    public function get($name)
    {
        return $this->$name;
    }

    /**
     * Get push format.
     *
     * @param string $push
     * @param mixed $data
     *
     * @return \Illuminate\View\View
     */
    protected function getPush($push, $data = [])
    {
        return view('admin::partials.push', ['key' => $push, 'value' => $data]);
    }

    /**
     * Push to content.
     *
     * @param string $push
     * @param mixed $data
     * @param array|null $viewData
     *
     * @return $this
     */
    public function push($push, $data = [], $viewData = null)
    {
        if (is_array($push)) {
            foreach ($push as $key => $value) {
                call_user_func([$this, 'push'], $key, $value, $viewData);
            }

            return $this;
        }

        if ($viewData !== null) {
            if (!$data instanceof Renderable) {
                $data = view($data, $viewData);
            } else {
                $data->with($viewData);
            }
        }

        $this->add($this->getPush($push, $data), true);

        return $this;
    }

    /**
     * Content datas.
     *
     * @param array $datas
     *
     * @return $this
     */
    public function data($datas)
    {
        $this->datas = $datas;

        return $this;

    }

    /**
     * Shift content datas.
     *
     * @param string $key
     *
     * @return array
     */
    protected function shiftData($key)
    {
        $data = [];

        if (isset($this->datas[$key])) {
            $data = array_shift($this->datas[$key]);
        }

        return $data;
    }

    /**
     * Content layout.
     *
     * @param string $layout
     * @param array|null $data
     *
     * @return $this
     */
    public function layout($layout, $data = null)
    {
        $this->layout = view($layout, $data ?? $this->shiftData($layout));

        $this->add($this->layout, true);

        return $this;
    }

    /**
     * Find view.
     *
     * @param string $view
     *
     * @return $this
     */
    public function find($view)
    {
        $get = View::where('slug', $view)->get()->first();

        if (!$get) {
            throw new \InvalidArgumentException("View [{$view}] not found.");
        }

        $this->view = $get;

        return $this;
    }

    /**
     * Push Content assets.
     */
    protected function pushAssets()
    {
        $this->view->allAssets()->sortBy(function ($item, $key) {
            return $item->pivot->sort;
        })->each(function ($item, $key) {
            $this->add($item, true);
        });
    }

    /**
     * Push Content blades.
     */
    protected function pushBlades()
    {
        $this->view->isNotLayout()->sortBy(function ($item, $key) {
            return $item->pivot->sort;
        })->each(function ($item, $key) {
            $this->add($item, true);
        });
    }

    /**
     * Append view to content.
     *
     * @param string $view
     *
     * @return $this
     */
    public function view($view)
    {
        $this->find($view);

        if ($layout = $this->view->isLayout()->first()) {
            $this->layout($layout->slug);
        }

        $this->pushBlades();

        $this->pushAssets();

        return $this;
    }

    /**
     * Add value to content.
     *
     * @param mixed $value
     * @param bool  $force
     *
     * @return $this
     */
    public function add($value, $force = false)
    {
        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if (!$force && is_string($value)) {
            $value = htmlentities($value, ENT_COMPAT, 'UTF-8');
        }

        array_push($this->contents, $value);

        return $this;
    }

    /**
     * Append \Illuminate\View\View for content.
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

        $this->add($view, true);

        return $this;
    }

    /**
     * Build content.
     *
     * @return array
     */
    public function build()
    {
        $build = [];

        foreach ($this->contents as $key => $item) {
            if ($item instanceof Renderable) {
                if (!$this->view && $this->layout) {
                    if ($item == $this->layout) {
                        $layout = count($build);
                        $item   = $this->layout->with($this->shiftData($this->layout->name()));
                    } else {
                        if ('admin::partials.push' == $item->name()) {
                            foreach ($item->getData() as $value) {
                                if ($value instanceof Renderable) {
                                    $value->with($this->shiftData($value->name()));
                                }
                            }
                            $build[$layout]->with([$item]);
                            continue;
                        } else {
                            $item->with($this->shiftData($item->name()));
                        }
                    }
                } else {
                    $item->with($this->shiftData($item->name()));
                }
            } elseif ($item instanceof Blade) {
                $render = view($item->slug, $this->shiftData($item->slug));

                if (!empty($type = $item->pivot->type) && $this->layout) {
                    $this->layout->with([$this->getPush($type, $render)]);
                    continue;
                }
                $item = $render;

            } elseif ($item instanceof Asset) {
                if (!empty($type = $item->pivot->type) && $this->layout) {
                    $this->layout->with([$this->getPush($type, $item->asset)]);
                    continue;
                }
                $item = $item->asset;
            }

            array_push($build, $item);
        }

        return $build;
    }

    /**
     * Render this content.
     *
     * @return string
     */
    public function render()
    {
        $build = $this->build();

        ob_start();

        foreach ($build as $content) {
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
