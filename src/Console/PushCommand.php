<?php

namespace Huztw\Admin\Console;

use Huztw\Admin\Database\Seeder\ActionSeeder;
use Huztw\Admin\Database\Seeder\BladeSeeder;
use Huztw\Admin\Database\Seeder\PermissionSeeder;
use Huztw\Admin\Database\Seeder\RouteSeeder;
use Huztw\Admin\Database\Seeder\ScriptSeeder;
use Huztw\Admin\Database\Seeder\StyleSeeder;
use Huztw\Admin\Database\Seeder\ViewSeeder;

class PushCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push the admin setting';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('<info>Pushing table</info>');

        $this->line('Route table :');

        $this->call('db:seed', ['--class' => RouteSeeder::class]);

        $this->line('Permission table :');

        $this->call('db:seed', ['--class' => PermissionSeeder::class]);

        $this->line('Action table :');

        $this->call('db:seed', ['--class' => ActionSeeder::class]);

        $this->line('View table :');

        $this->call('db:seed', ['--class' => ViewSeeder::class]);

        $this->line('Blade table :');

        $this->call('db:seed', ['--class' => BladeSeeder::class]);

        $this->line('Style table :');

        $this->call('db:seed', ['--class' => StyleSeeder::class]);

        $this->line('Script table :');

        $this->call('db:seed', ['--class' => ScriptSeeder::class]);

        $this->line('<info>Pushing complete</info>');
    }
}
