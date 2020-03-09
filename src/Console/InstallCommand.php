<?php

namespace Huztw\Admin\Console;

use Illuminate\Console\Command as BaseCommand;

class InstallCommand extends BaseCommand
{
    use Command;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the admin package';

    /**
     * Install directory.
     *
     * @var string
     */
    protected $directory = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->initDatabase();

        $this->initAdminDirectory();
    }

    /**
     * Create tables and seed it.
     *
     * @return void
     */
    public function initDatabase()
    {
        $this->call('migrate');

        $userModel = config('admin.database.users_model');

        if ($userModel::count() == 0) {
            $this->call('db:seed', ['--class' => \Huztw\Admin\Database\Auth\AdminSeeder::class]);
        }
    }

    /**
     * Initialize the admAin directory.
     *
     * @return void
     */
    protected function initAdminDirectory()
    {
        $this->directory = config('admin.directory');

        if (is_dir($this->directory)) {
            $this->line("<error>{$this->directory} directory already exists !</error> ");

            return;
        }

        $this->makeDir('/');

        $this->makeDir('Controllers');

        $this->createControllers();

        $this->createBootstrapFile();

        $this->createRoutesFile();
    }

    /**
     * Create Controllers.
     *
     * @return void
     */
    public function createControllers()
    {
        $homeController = $this->directory . '/Controllers/HomeController.php';
        $this->makefile($homeController, $this->getStub('HomeController'));

        $loginController = $this->directory . '/Controllers/LoginController.php';
        $this->makefile($loginController, $this->getStub('LoginController'));

        $exampleController = $this->directory . '/Controllers/ExampleController.php';
        $this->makefile($exampleController, $this->getStub('ExampleController'));
    }

    /**
     * Create bootstrap file.
     *
     * @return void
     */
    protected function createBootstrapFile()
    {
        $file = $this->directory . '/bootstrap.php';
        $this->makefile($file, $this->getStub('bootstrap'));
    }

    /**
     * Create routes file.
     *
     * @return void
     */
    protected function createRoutesFile()
    {
        $file = $this->directory . '/routes.php';
        $this->makefile($file, $this->getStub('routes'));
    }
}
