<?php

namespace Huztw\Admin\Database\Seeds;

use Carbon\Carbon;
use Huztw\Admin\Database\Layout\View;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ViewSeeder extends Seeder
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

        $this->items = collect($this->items)->sortBy('view')->values()->toArray();

        // insert to database.
        View::insertOrIgnore($this->items);

        // reset AUTO_INCREMENT
        $increments = View::max('id') + 1;
        DB::statement("ALTER TABLE " . View::table() . " AUTO_INCREMENT = " . $increments);
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

        return $settings['views'] ?? [];
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
     * @param $views
     *
     * @return void
     */
    protected function settings($views)
    {
        $origins = $this->originData();

        foreach (array_diff($views, $origins) as $name => $view) {
            $name = is_int($name) ? null : $name;
            $this->setItems(['view' => $view, 'name' => $name]);
        }

        foreach (array_diff($origins, $views) as $id => $view) {
            View::destroy($id);
        }

        foreach (array_intersect($origins, $views) as $id => $view) {
            $filter = Arr::where($views, function ($value, $key) use ($view) {
                return $value == $view;
            });

            $name = key($filter);
            $name = is_int($name) ? null : $name;

            View::find($id)->update(['name' => $name]);
        }
    }

    /**
     * Get the original data.
     *
     * @return array
     */
    protected function originData()
    {
        $data = [];

        foreach (View::get() as $item) {
            $data[$item->id] = $item->view;
        }

        return $data;
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
            'view'       => $item['view'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
