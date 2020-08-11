<?php

namespace Huztw\Admin\Console;

use Illuminate\Console\Command;

class ViewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Admin view list.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->show();
    }

    /**
     * Show admin user list.
     */
    protected function show()
    {
        $viewModel = config('admin.database.views_model');

        $headers = ['Views', 'Blades', 'Assets'];

        $list = [];
        foreach ($viewModel::all() as $view) {
            $blades = $view->blades;

            $assets = $view->allAssets();

            $data = [
                $view->view,
                implode("\n", $blades->pluck('blade')->toArray()),
                implode("\n", $assets->pluck('asset')->toArray()),
            ];

            array_push($list, $data);
        }

        $this->table($headers, $list);
    }
}
