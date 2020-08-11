<?php

namespace Huztw\Admin\Database\Seeds;

use Carbon\Carbon;
use Huztw\Admin\Database\Layout\Asset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AssetSeeder extends Seeder
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

        $this->items = collect($this->items)->sortBy('asset')->values()->toArray();

        // insert to database.
        Asset::insertOrIgnore($this->items);

        // reset AUTO_INCREMENT
        $increments = Asset::max('id') + 1;
        DB::statement("ALTER TABLE " . Asset::table() . " AUTO_INCREMENT = " . $increments);
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

        return $settings['assets'] ?? [];
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
     * @param $assets
     *
     * @return void
     */
    protected function settings($assets)
    {
        $origins = $this->originData();

        foreach (array_diff($assets, $origins) as $name => $asset) {
            $name = is_int($name) ? null : $name;
            $this->setItems(['asset' => $asset, 'name' => $name]);
        }

        foreach (array_diff($origins, $assets) as $id => $asset) {
            Asset::destroy($id);
        }

        foreach (array_intersect($origins, $assets) as $id => $asset) {
            $filter = Arr::where($assets, function ($value, $key) use ($asset) {
                return $value == $asset;
            });

            $name = key($filter);
            $name = is_int($name) ? null : $name;

            Asset::find($id)->update(['name' => $name]);
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

        foreach (Asset::get() as $item) {
            $data[$item->id] = $item->asset;
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
            'asset'      => htmlentities($item['asset'], ENT_COMPAT, 'UTF-8'),
            'name'       => $item['name'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
