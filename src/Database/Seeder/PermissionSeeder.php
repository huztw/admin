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
            $this->settingsByAuto();

            $settings = array_merge_recursive($settings, $this->getSettings());
        }

        $this->settings($settings);

        // insert to database.
        Permission::insertOrIgnore($this->getPermissions());

        // reset AUTO_INCREMENT
        $increments = Permission::max('id') + 1;
        DB::statement("ALTER TABLE " . config('admin.database.permissions_table') . " AUTO_INCREMENT = " . $increments);
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
     * Set the already exist permissions settings automatically.
     *
     * @return void
     */
    protected function settingsByAuto()
    {
        //......
    }

    /**
     * Get the permissions settings.
     *
     * @param $routes
     *
     * @return void
     */
    protected function settings($permissions)
    {
        foreach ($permissions as $slug => $name) {
            if ($this->isPermissionExist(['slug' => $slug, 'name' => $name])) {
                continue;
            }

            $this->setPermissions([
                'name' => $name,
                'slug' => $slug,
            ]);
        }
    }

    /**
     * Check the permission for existence.
     *
     * @return bool
     */
    protected function isPermissionExist($permission)
    {
        $exist = false;

        foreach ($this->permissions as $key => $finished) {
            if ($permission['slug'] == $finished['slug']) {
                $this->permissions[$key]['name'] = $permission['name'];

                $exist = true;
            }
        }

        return $exist;
    }

    /**
     * Set the permissions.
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
}
