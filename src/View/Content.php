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
    protected $build = [];

    /**
     * @var array
     */
    protected $push = [];

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
     * Compile with push.
     *
     * @return void
     */
    protected function withPush()
    {
        if ($this->layout) {
            $this->layout->with(array_map(function ($item) {
                $push = key($item);
                $data = current($item);

                if ($data instanceof Renderable) {
                    $data->with($this->shiftData($data->name()));
                }

                return view('admin::partials.push', ['key' => $push, 'value' => $data]);
            }, $this->push));
        } elseif (count($this->push) > 0) {
            throw new \InvalidArgumentException("Layout [{$this->layout}] not found.");
        }
    }

    /**
     * Push to content.
     *
     * @param string $push
     * @param mixed $datas
     *
     * @return $this
     */
    public function push($push, ...$datas)
    {
        foreach ($datas as $data) {
            array_push($this->push, [$push => $data]);
        }

        return $this;
    }

    /**
     * Datas for content.
     *
     * @param string|array $view
     * @param array $datas
     *
     * @return $this
     */
    public function data($view, ...$datas)
    {
        if (count($datas) == 0) {
            array_push($this->datas, $view);
        } else {
            foreach ($datas as $data) {
                if (isset($this->datas[$view])) {
                    array_push($this->datas[$view], $data);
                } else {
                    $this->datas[$view] = [$data];
                }
            }
        }

        return $this;
    }

    /**
     * Shift content's datas.
     *
     * @param string $key
     *
     * @return array
     */
    protected function shiftData($key)
    {
        $data = Arr::collapse(Arr::where($this->datas, function ($value, $key) {
            return is_int($key);
        }));

        if (isset($this->datas[$key])) {
            $data = array_merge($data, array_shift($this->datas[$key]) ?? []);
        }

        return $data;
    }

    /**
     * Layout for content.
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
     * Push assets for content.
     */
    protected function pushAssets()
    {
        $this->view->allAssets()->sortBy(function ($item, $key) {
            return $item->pivot->sort;
        })->each(function ($item, $key) {
            if (!empty($type = $item->pivot->type) && $this->layout) {
                $this->push($type, $item->asset);
            } else {
                $this->add($item->asset, true);
            }
        });
    }

    /**
     * Push blades for content.
     */
    protected function pushBlades()
    {
        $this->view->blades->sortBy(function ($item, $key) {
            return $item->pivot->sort;
        })->each(function ($item, $key) {
            if (!empty($type = $item->pivot->type) && $this->layout) {
                $this->push($type, view($item->slug));
            } else {
                $this->add(view($item->slug), true);
            }
        });
    }

    /**
     * Build view for content.
     *
     * @param string $view
     *
     * @return $this
     */
    public function view($view)
    {
        $this->find($view);

        if ($layout = $this->view->layout) {
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

        array_push($this->build, $value);

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
     * @return void
     */
    public function build()
    {
        $this->withPush();

        $this->build = array_map(function ($item) {
            if ($item instanceof Renderable) {
                $item->with($this->shiftData($item->name()));
            }

            return $item;
        }, $this->build);
    }

    /**
     * Render this content.
     *
     * @return string
     */
    public function render()
    {
        $this->build();

        ob_start();

        foreach ($this->build as $content) {
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
