<?php

namespace Huztw\Admin\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class UserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:user
                            {--add : Create a admin user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Admin users list. If you want create a admin user, you can add the `--add` option';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('add')) {
            $this->add();
        } else {
            $this->show();
        }
    }

    /**
     * Show admin user list.
     */
    protected function show()
    {
        $userModel = config('admin.database.users_model');

        $headers = ['User Name', 'Name', 'Roles', 'Permissions', 'Routes', 'Actions', 'Created', 'Updated'];

        $userslist = [];
        foreach ($userModel::all() as $user) {
            $roles = implode(',', $user->roles->pluck('name')->toArray());

            $userdata = [
                'username'   => $user->username,
                'name'       => $user->name,
                'roles'      => $roles,
                'permission' => implode("\n", $user->allPermissions(true)->pluck('name')->toArray()),
                'routes'     => implode("\n", $user->allRoutes(true)->pluck('http_path')->toArray()),
                'actions'    => implode("\n", $user->allActions(true)->pluck('name')->toArray()),
                'created_at' => date($user->created_at),
                'updated_at' => date($user->updated_at),
            ];

            array_push($userslist, $userdata);
        }

        $this->table($headers, $userslist);
    }

    /**
     * Create a admin user.
     */
    protected function add()
    {
        $userModel = config('admin.database.users_model');
        $roleModel = config('admin.database.roles_model');

        $username = null;

        while ($username === null) {
            $answer_username = $this->ask('Please enter a username to login');

            if ($userModel::where('username', '=', $answer_username)->exists()) {

                $this->line("<error>Username is exists: </error> $answer_username");
            } else {
                $username = $answer_username;
            }
        }

        $password = null;

        while ($password === null) {
            $answer_password = $this->secret('Please enter a password to login');

            $answer_password_again = $this->secret('Please enter the password again');

            if ($answer_password != $answer_password_again) {
                $this->line("<error>Password do not match</error>");
            } else {
                $password = bcrypt($answer_password);
            }
        }

        $name = $this->ask('Please enter a name to display');

        if ($this->confirm('Add role for user?', true)) {
            $roles = $roleModel::all();

            if (!$this->confirm('All role?', true)) {
                /** @var array $selected */
                $selectedOption = $roles->pluck('name')->toArray();

                $selected = $this->choice('Please choose a role for the user, or you can use "," to make multiple', $selectedOption, null, null, true);

                $selected = Arr::wrap($selected);

                $roles = $roles->filter(function ($role) use ($selected) {
                    return in_array($role->name, $selected);
                });
            }
        }

        $user = new $userModel(compact('username', 'password', 'name'));

        $user->save();

        if (isset($roles)) {
            $user->roles()->attach($roles);
        }

        $this->line('<info>User created successfully: </info> ' . $name);
    }
}
