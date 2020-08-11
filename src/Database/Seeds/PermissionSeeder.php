<?php

namespace Huztw\Admin\Database\Seeds;

use Carbon\Carbon;
use Huztw\Admin\Database\Auth\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
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

        $this->items = collect($this->items)->sortBy('permission')->values()->toArray();

        // insert to database.
        Permission::insertOrIgnore($this->items);

        // reset AUTO_INCREMENT
        $increments = Permission::max('id') + 1;
        DB::statement("ALTER TABLE " . Permission::table() . " AUTO_INCREMENT = " . $increments);
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

        return $settings['permissions'] ?? [];
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
     * @param $permissions
     *
     * @return void
     */
    protected function settings($permissions)
    {
        $origins = $this->originData();

        foreach (array_diff($permissions, $origins) as $name => $permission) {
            $name = is_int($name) ? $permission : $name;
            $this->setItems(['permission' => $permission, 'name' => $name]);
        }

        foreach (array_intersect($origins, $permissions) as $id => $permission) {
            $filter = Arr::where($permissions, function ($value, $key) use ($permission) {
                return $value == $permission;
            });

            $name = key($filter);
            $name = is_int($name) ? $permission : $name;

            Permission::find($id)->update(['name' => $name]);
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

        foreach (Permission::get() as $item) {
            $data[$item->id] = $item->permission;
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
            'permission' => $item['permission'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
