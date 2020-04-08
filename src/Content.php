<?php

namespace Huztw\Admin;

use Closure;
use Huztw\Admin\Database\Auth\View;
use Illuminate\Contracts\Support\Renderable;

class Content implements Renderable
{
    /**
     * Content title.
     *
     * @var string
     */
    protected $title = ' ';

    /**
     * Content description.
     *
     * @var string
     */
    protected $description = ' ';

    /**
     * Page breadcrumb.
     *
     * @var array
     */
    protected $breadcrumb = [];

    /**
     * @var array
     */
    protected $contents = [];

    /**
     * @var array
     */
    protected $view;

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
     * Set description of content.
     *
     * @param string $description
     *
     * @return $this
     */
    public function description($description = '')
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Append view content for content body.
     *
     * @param $view
     *
     * @return $this
     */
    public function view($view)
    {
        $views = array_map(function ($item) {
            return "view:$item";
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

        if ($blades) {
            return $blades->blades->pluck('slug')->toArray();
        }

        return [];
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
            if (is_string($content) && strpos($content, 'view:') === 0) {
                $content = view(substr($content, strlen('view:')));
            }

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
        return $this->build();
    }
}
