<?php

namespace Huztw\Admin\Console;

use Huztw\Admin\AdminServiceProvider;

class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:publish {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "re-publish huztw-admin's assets, configuration, language and migration files. If you want overwrite the existing files, you can add the `--force` option";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $force   = $this->option('force');
        $options = ['--provider' => AdminServiceProvider::class];
        if ($force == true) {
            $options['--force'] = true;
        }
        $this->call('vendor:publish', $options);
        $this->call('view:clear');
    }
}
