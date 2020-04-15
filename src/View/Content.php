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
     * Push to content.
     *
     * @param string $push
     * @param mixed $data
     *
     * @return $this
     */
    public function push($push, $data = [])
    {
        if (is_array($push)) {
            foreach ($push as $key => $value) {
                call_user_func([$this, 'push'], $key, $value);
            }

            return $this;
        }

        $this->append('admin::partials.push', ['key' => $push, 'value' => $data]);

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
            throw new \InvalidArgumentException("View [{$view}] not found.");
        }

        $this->view = $get;

        return $this;
    }

    /**
     * Push Content assets.
     *
     * @return $this
     */
    protected function pushAssets()
    {
        $assets = $this->view->allAssets()->sortBy(function ($item, $key) {
            return $item->pivot->sort;
        });

        foreach ($assets as $asset) {
            if (empty($type = $asset->pivot->type)) {
                $this->add($asset->asset, true);
            } else {
                $this->push($type, $asset->asset);
            }
        }

        return $this;
    }

    /**
     * Push Content blades.
     *
     * @param array $data
     *
     * @return $this
     */
    protected function pushBlades($data = [])
    {
        $blades = $this->view->isNotLayout()->sortBy(function ($item, $key) {
            return $item->pivot->sort;
        });

        foreach ($blades as $blade) {
            $slug = $blade->slug;

            if (isset($data[$slug])) {
                $shift = array_shift($data[$slug]);
            } else {
                $shift = Arr::first($data, function ($value, $key) use (&$data) {
                    if (is_int($key)) {
                        unset($data[$key]);
                        return true;
                    }
                });
            }

            $view = view($slug, $shift ?? []);

            if (empty($type = $blade->pivot->type)) {
                $this->append($view);
            } else {
                $this->push($type, $view->render());
            }
        }

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

        if ($layout = $this->view->isLayout()->first()) {
            $this->layout($layout->slug, $mergeData);
        }

        $this->pushBlades($data);

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

        if (!$force) {
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
     * Render this content.
     *
     * @return string
     */
    public function render()
    {
        ob_start();

        if ($this->layout) {
            foreach ($this->contents as $key => $content) {
                if ($content instanceof Renderable) {
                    $this->layout->with($key, $content);
                    unset($this->contents[$key]);
                }
            }

            echo $this->layout->render();

            foreach ($this->contents as $content) {
                if ($content instanceof Closure) {
                    $content();
                } else {
                    echo (string) $content;
                }
            }
        } else {
            foreach ($this->contents as $content) {
                if ($content instanceof Renderable) {
                    echo $content->render();
                } elseif ($content instanceof Closure) {
                    $content();
                } else {
                    echo (string) $content;
                }
            }

        }

        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }
}
