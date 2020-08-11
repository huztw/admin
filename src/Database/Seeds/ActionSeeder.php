<?php

namespace Huztw\Admin\Database\Seeds;

use Carbon\Carbon;
use Huztw\Admin\Database\Auth\Action;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ActionSeeder extends Seeder
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

        $this->items = collect($this->items)->sortBy('action')->values()->toArray();

        // insert to database.
        Action::insertOrIgnore($this->items);

        // reset AUTO_INCREMENT
        $increments = Action::max('id') + 1;
        DB::statement("ALTER TABLE " . Action::table() . " AUTO_INCREMENT = " . $increments);
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

        return $settings['actions'] ?? [];
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
     * @param $actions
     *
     * @return void
     */
    protected function settings($actions)
    {
        $origins = $this->originData();

        foreach (array_diff($actions, $origins) as $name => $action) {
            $name = is_int($name) ? $action : $name;
            $this->setItems(['action' => $action, 'name' => $name]);
        }

        foreach (array_intersect($origins, $actions) as $id => $action) {
            $filter = Arr::where($actions, function ($value, $key) use ($action) {
                return $value == $action;
            });

            $name = key($filter);
            $name = is_int($name) ? $action : $name;

            Action::find($id)->update(['name' => $name]);
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

        foreach (Action::get() as $item) {
            $data[$item->id] = $item->action;
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
            'action'     => $item['action'],
            'name'       => $item['name'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
