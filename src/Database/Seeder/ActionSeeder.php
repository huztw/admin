<?php

namespace Huztw\Admin\Database\Seeder;

use Carbon\Carbon;
use Huztw\Admin\Database\Auth\Action;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActionSeeder extends Seeder
{
    /**
     * @var array
     */
    protected $actions = [];

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
        Action::insertOrIgnore($this->getActions());

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
     * Get the actions settings.
     *
     * @param $actions
     *
     * @return void
     */
    protected function settings($actions)
    {
        $slugs = array_keys($actions);

        $origins = $this->originSlugs();

        foreach (array_diff($slugs, $origins) as $slug) {
            $this->setActions(['slug' => $slug, 'name' => $actions[$slug]]);
        }

        foreach (array_diff($origins, $slugs) as $id => $slug) {
            Action::destroy($id);
        }

        foreach (array_intersect($origins, $slugs) as $id => $slug) {
            $originAction = Action::find($id);

            $originAction->name = $actions[$slug];

            $originAction->save();
        }
    }

    /**
     * Get the original actions's slug.
     *
     * @return array
     */
    protected function originSlugs()
    {
        $actions = [];

        foreach (Action::get() as $action) {
            $actions[$action->id] = $action->slug;
        }

        return $actions;
    }

    /**
     * Set the actions.
     *
     * @param $action
     *
     * @return void
     */
    protected function setActions($action)
    {
        array_push($this->actions, [
            'name'       => $action['name'],
            'slug'       => $action['slug'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    /**
     * Get the actions.
     *
     * @return array
     */
    protected function getActions()
    {
        $actions = $this->actions;

        // Sort the actions
        $slugs = [];

        foreach ($actions as $key => $action) {
            $slugs[$key] = $action['slug'];
        }

        array_multisort($slugs, SORT_ASC, $actions);

        return $actions;
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
