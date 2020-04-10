<?php

namespace Huztw\Admin\Database\Seeder;

use Carbon\Carbon;
use Huztw\Admin\Database\Layout\Style;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StyleSeeder extends Seeder
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = $this->getSettings(__DIR__ . '/../../');

        // Check if isn't first time run
        if (file_exists($this->settingsFile())) {
            $settings = array_merge($settings, $this->getSettings());
        }

        $this->settings($settings);

        $this->items = collect($this->items)->sortBy('slug')->values()->toArray();

        // insert to database.
        Style::insertOrIgnore($this->items);

        // reset AUTO_INCREMENT
        $increments = Style::max('id') + 1;
        DB::statement("ALTER TABLE " . Style::table() . " AUTO_INCREMENT = " . $increments);
    }

    /**
     * Get the settings.
     *
     * @param $directory
     *
     * @return array
     */
    protected function getSettings($directory = null): array
    {
        $settings = require $this->settingsFile($directory);

        return $settings['styles'] ?? [];
    }

    /**
     * Get the settings file.
     *
     * @param $directory
     *
     * @return string
     */
    protected function settingsFile($directory = null)
    {
        $directory = $directory ?? trim(config('admin.directory'), '/');

        $file = $directory . '/push.php';

        return str_replace('/', DIRECTORY_SEPARATOR, $file);
    }

    /**
     * Start settings.
     *
     * @param $settings
     *
     * @return void
     */
    protected function settings($settings)
    {
        $slugs = array_keys($settings);

        $origins = $this->originSlugs();

        foreach (array_diff($slugs, $origins) as $slug) {
            if (is_array($settings[$slug])) {
                $name  = !is_int($name = key($settings[$slug])) ? $name : null;
                $style = current($settings[$slug]);
            } else {
                $style = $settings[$slug];
            }

            $this->setItems([
                'slug'  => $slug,
                'name'  => $name ?? null,
                'style' => $style,
            ]);
        }

        foreach (array_diff($origins, $slugs) as $id => $slug) {
            Style::destroy($id);
        }

        foreach (array_intersect($origins, $slugs) as $id => $slug) {
            if (is_array($settings[$slug])) {
                $name  = !is_int($name = key($settings[$slug])) ? $name : null;
                $style = current($settings[$slug]);
            } else {
                $style = $settings[$slug];
            }

            $origin = Style::find($id);

            $origin->name = $name ?? null;

            $origin->style = $style;

            $origin->save();
        }
    }

    /**
     * Get the original slugs.
     *
     * @return array
     */
    protected function originSlugs()
    {
        $slugs = [];

        foreach (Style::get() as $item) {
            $slugs[$item->id] = $item->slug;
        }

        return $slugs;
    }

    /**
     * Set the items.
     *
     * @param $item
     *
     * @return void
     */
    protected function setItems($item)
    {
        array_push($this->items, [
            'name'       => $item['name'],
            'slug'       => $item['slug'],
            'style'      => htmlentities($item['style'], ENT_COMPAT, 'UTF-8'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
