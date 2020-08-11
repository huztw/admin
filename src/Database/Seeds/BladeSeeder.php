<?php

namespace Huztw\Admin\Database\Seeds;

use Carbon\Carbon;
use Huztw\Admin\Database\Layout\Blade;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BladeSeeder extends Seeder
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
        $settings = $this->getBlades();

        $settings = array_merge($settings, collect($this->getSettings(__DIR__ . '/../../'))->intersectByKeys($settings)->toArray());

        // Check if isn't first time run
        if (file_exists($this->settingsFile())) {
            $settings = array_merge($settings, collect($this->getSettings())->intersectByKeys($settings)->toArray());
        }

        $this->settings($settings);

        $this->items = collect($this->items)->sortBy('blade')->values()->toArray();

        // insert to database.
        Blade::insertOrIgnore($this->items);

        // reset AUTO_INCREMENT
        $increments = Blade::max('id') + 1;
        DB::statement("ALTER TABLE " . Blade::table() . " AUTO_INCREMENT = " . $increments);
    }

    /**
     * Get the blades.
     *
     * @return array
     */
    protected function getBlades()
    {
        $admin = [];

        // $admin = collect(File::allFiles(__DIR__ . '/../../' . 'resources' . DIRECTORY_SEPARATOR . 'views'))->map(function ($item) {
        //     return 'admin::' . str_replace(DIRECTORY_SEPARATOR, '.', strstr($item->getRelativePathname(), '.blade.php', true));
        // })->filter()->flip()->map(function () {
        //     return null;
        // })->toArray();

        $default = collect(File::allFiles(resource_path('views')))->map(function ($item) {
            return str_replace(DIRECTORY_SEPARATOR, '.', strstr($item->getRelativePathname(), '.blade.php', true));
        })->filter()->all();

        return array_merge($admin, $default);
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

        return $settings['blades'] ?? [];
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
     * @param $blades
     *
     * @return void
     */
    protected function settings($blades)
    {
        $origins = $this->originData();

        foreach (array_diff($blades, $origins) as $name => $blade) {
            $name = is_int($name) ? null : $name;
            $this->setItems(['blade' => $blade, 'name' => $name]);
        }

        foreach (array_diff($origins, $blades) as $id => $blade) {
            Blade::destroy($id);
        }

        foreach (array_intersect($origins, $blades) as $id => $blade) {
            $filter = Arr::where($blades, function ($value, $key) use ($blade) {
                return $value == $blade;
            });

            $name = key($filter);
            $name = is_int($name) ? null : $name;

            Blade::find($id)->update(['name' => $name]);
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

        foreach (Blade::get() as $item) {
            $data[$item->id] = $item->blade;
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
            'blade'      => $item['blade'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
