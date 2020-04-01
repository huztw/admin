<?php

namespace Huztw\Admin\Console;

use Huztw\Admin\Database\Seeder\ActionSeeder;
use Huztw\Admin\Database\Seeder\PermissionSeeder;
use Huztw\Admin\Database\Seeder\RouteSeeder;

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

        $this->call('db:seed', ['--class' => RouteSeeder::class]);

        $this->call('db:seed', ['--class' => PermissionSeeder::class]);

        $this->call('db:seed', ['--class' => ActionSeeder::class]);

        $this->line('<info>Pushing complete</info>');
    }
}
