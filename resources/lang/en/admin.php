<?php

return [
    'online'                => 'Online',
    'login'                 => 'Login',
    'logout'                => 'Logout',
    'setting'               => 'Setting',
    'name'                  => 'Name',
    'username'              => 'Username',
    'password'              => 'Password',
    'password_confirmation' => 'Password confirmation',
    'remember_me'           => 'Remember me',
    'user_setting'          => 'User setting',
    'avatar'                => 'Avatar',
    'list'                  => 'List',
    'new'                   => 'New',
    'create'                => 'Create',
    'delete'                => 'Delete',
    'remove'                => 'Remove',
    'edit'                  => 'Edit',
    'view'                  => 'View',
    'continue_editing'      => 'Continue editing',
    'continue_creating'     => 'Continue creating',
    'detail'                => 'Detail',
    'browse'                => 'Browse',
    'reset'                 => 'Reset',
    'export'                => 'Export',
    'batch_delete'          => 'Batch delete',
    'save'                  => 'Save',
    'refresh'               => 'Refresh',
    'order'                 => 'Order',
    'expand'                => 'Expand',
    'collapse'              => 'Collapse',
    'filter'                => 'Filter',
    'search'                => 'Search',
    'close'                 => 'Close',
    'show'                  => 'Show',
    'entries'               => 'entries',
    'captcha'               => 'Captcha',
    'action'                => 'Action',
    'title'                 => 'Title',
    'description'           => 'Description',
    'back'                  => 'Back',
    'back_to_list'          => 'Back to List',
    'submit'                => 'Submit',
    'menu'                  => 'Menu',
    'input'                 => 'Input',
    'succeeded'             => 'Succeeded',
    'failed'                => 'Failed',
    'delete_confirm'        => 'Are you sure to delete this item ?',
    'delete_succeeded'      => 'Delete succeeded !',
    'delete_failed'         => 'Delete failed !',
    'update_succeeded'      => 'Update succeeded !',
    'save_succeeded'        => 'Save succeeded !',
    'refresh_succeeded'     => 'Refresh succeeded !',
    'login_successful'      => 'Login successful',
    'login_failed'          => 'Login failed',
    'choose'                => 'Choose',
    'choose_file'           => 'Select file',
    'choose_image'          => 'Select image',
    'more'                  => 'More',
    'deny'                  => 'Permission denied',
    'administrator'         => 'Administrator',
    'roles'                 => 'Roles',
    'permissions'           => 'Permissions',
    'slug'                  => 'Slug',
    'created_at'            => 'Created At',
    'updated_at'            => 'Updated At',
    'alert'                 => 'Alert',
    'parent_id'             => 'Parent',
    'icon'                  => 'Icon',
    'uri'                   => 'URI',
    'operation_log'         => 'Operation log',
    'parent_select_error'   => 'Parent select error',
    'pagination'            => [
        'range' => 'Showing :first to :last of :total entries',
    ],
    'role'                  => 'Role',
    'permission'            => 'Permission',
    'route'                 => 'Route',
    'confirm'               => 'Confirm',
    'cancel'                => 'Cancel',
    'http'                  => [
        'method' => 'HTTP method',
        'path'   => 'HTTP path',
        'status' => [
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            419 => 'Page Expired',
            423 => 'Not Found',
            429 => 'Too Many Requests',
            500 => 'Server Error',
            503 => 'Service Unavailable',
        ],
    ],
    'all_methods_if_empty'  => 'All methods if empty',
    'all'                   => 'All',
    'current_page'          => 'Current page',
    'selected_rows'         => 'Selected rows',
    'upload'                => 'Upload',
    'new_folder'            => 'New folder',
    'time'                  => 'Time',
    'size'                  => 'Size',
    'listbox'               => [
        'text_total'         => 'Showing all {0}',
        'text_empty'         => 'Empty list',
        'filtered'           => '{0} / {1}',
        'filter_clear'       => 'Show all',
        'filter_placeholder' => 'Filter',
    ],
    'grid_items_selected'   => '{n} items selected',

    'menu_titles'           => [],
    'prev'                  => 'Prev',
    'next'                  => 'Next',
    'quick_create'          => 'Quick create',

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
     */

    'auth'                  => [
        'failed'   => 'These credentials do not match our records.',
        'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
     */

    'validation'            => [
        'alpha_unicode'      => 'The :attribute may only contain letters.',
        'alpha_dash_unicode' => 'The :attribute may only contain letters, numbers, dashes and underscores.',
        'alpha_num_unicode'  => 'The :attribute may only contain letters and numbers.',
    ],
];
