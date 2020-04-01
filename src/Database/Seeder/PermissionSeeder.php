<?php

namespace Huztw\Admin\Database\Seeder;

use Carbon\Carbon;
use Huztw\Admin\Database\Auth\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * @var array
     */
    protected $permissions = [];

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
            $settings = $this->array_merge($settings, $this->getSettings());
        }

        $this->settings($settings);

        // insert to database.
        Permission::insertOrIgnore($this->getPermissions());

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
     * Get the permissions settings.
     *
     * @param $permissions
     *
     * @return void
     */
    protected function settings($permissions)
    {
        foreach ($permissions as $slug => $name) {
            $permission = ['slug' => $slug, 'name' => $name];

            if ($this->isPermissionExist($permission)) {
                continue;
            }

            $this->setPermissions($permission);
        }
    }

    /**
     * Check the permission for existence.
     *
     * @param $permission
     *
     * @return bool
     */
    protected function isPermissionExist($permission): bool
    {
        foreach (Permission::get() as $origin) {
            if ($permission['slug'] == $origin->slug) {
                if ($permission['name'] != $origin->name) {
                    $originPermission = Permission::find($origin->id);

                    $originPermission->name = $permission['name'];

                    $originPermission->save();
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Set the permissions.
     *
     * @param $permission
     *
     * @return void
     */
    protected function setPermissions($permission)
    {
        array_push($this->permissions, [
            'name'       => $permission['name'],
            'slug'       => $permission['slug'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    /**
     * Get the permissions.
     *
     * @return array
     */
    protected function getPermissions()
    {
        $permissions = $this->permissions;

        // Sort the permissions
        $slugs = [];

        foreach ($permissions as $key => $permission) {
            $slugs[$key] = $permission['slug'];
        }

        array_multisort($slugs, SORT_ASC, $permissions);

        return $permissions;
    }

    /**
     * merge the array.
     *
     * @param $arrays
     *
     * @return array
     */
    protected function array_merge(...$arrays): array
    {
        $merge = array_shift($arrays);

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                $merge[$key] = $value;
            }
        }

        return $merge;
    }
}
