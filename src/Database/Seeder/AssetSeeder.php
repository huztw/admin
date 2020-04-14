<?php

namespace Huztw\Admin\Database\Seeder;

use Carbon\Carbon;
use Huztw\Admin\Database\Layout\Asset;
use Illuminate\Database\Seeder;
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

        $this->items = collect($this->items)->sortBy('slug')->values()->toArray();

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
                $asset = current($settings[$slug]);
            } else {
                $asset = $settings[$slug];
            }

            $this->setItems([
                'slug'  => $slug,
                'name'  => $name ?? null,
                'asset' => $asset,
            ]);
        }

        foreach (array_diff($origins, $slugs) as $id => $slug) {
            Asset::destroy($id);
        }

        foreach (array_intersect($origins, $slugs) as $id => $slug) {
            if (is_array($settings[$slug])) {
                $name  = !is_int($name = key($settings[$slug])) ? $name : null;
                $asset = current($settings[$slug]);
            } else {
                $asset = $settings[$slug];
            }

            $origin = Asset::find($id);

            $origin->name = $name ?? null;

            $origin->asset = $asset;

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

        foreach (Asset::get() as $item) {
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
            'slug'       => $item['slug'],
            'name'       => $item['name'],
            'asset'      => htmlentities($item['asset'], ENT_COMPAT, 'UTF-8'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
