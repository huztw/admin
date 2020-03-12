<?php

return [
    'online'                => '在線',
    'login'                 => '登入',
    'logout'                => '登出',
    'setting'               => '設置',
    'name'                  => '名稱',
    'username'              => '用戶名',
    'password'              => '密碼',
    'password_confirmation' => '確認密碼',
    'remember_me'           => '記住我',
    'user_setting'          => '用戶設置',
    'avatar'                => '頭像',
    'list'                  => '列表',
    'new'                   => '新增',
    'create'                => '創建',
    'delete'                => '刪除',
    'remove'                => '移除',
    'edit'                  => '編輯',
    'view'                  => '查看',
    'continue_editing'      => '繼續編輯',
    'continue_creating'     => '繼續創建',
    'detail'                => '詳細',
    'browse'                => '瀏覽',
    'reset'                 => '重置',
    'export'                => '匯出',
    'batch_delete'          => '批次刪除',
    'save'                  => '儲存',
    'refresh'               => '重新整理',
    'order'                 => '排序',
    'expand'                => '展開',
    'collapse'              => '收起',
    'filter'                => '篩選',
    'search'                => '搜尋',
    'close'                 => '關閉',
    'show'                  => '顯示',
    'entries'               => '條',
    'captcha'               => '驗證碼',
    'action'                => '操作',
    'title'                 => '標題',
    'description'           => '簡介',
    'back'                  => '返回',
    'back_to_list'          => '返回列表',
    'submit'                => '送出',
    'menu'                  => '目錄',
    'input'                 => '輸入',
    'succeeded'             => '成功',
    'failed'                => '失敗',
    'delete_confirm'        => '確認刪除？',
    'delete_succeeded'      => '刪除成功！',
    'delete_failed'         => '刪除失敗！',
    'update_succeeded'      => '更新成功！',
    'save_succeeded'        => '儲存成功！',
    'refresh_succeeded'     => '成功重新整理！',
    'login_successful'      => '成功登入！',
    'login_failed'          => '登入失敗！',
    'choose'                => '選擇',
    'choose_file'           => '選擇檔案',
    'choose_image'          => '選擇圖片',
    'more'                  => '更多',
    'deny'                  => '權限不足',
    'administrator'         => '管理員',
    'roles'                 => '角色',
    'permissions'           => '權限',
    'slug'                  => '標誌',
    'created_at'            => '建立時間',
    'updated_at'            => '更新時間',
    'alert'                 => '警告',
    'parent_id'             => '父目錄',
    'icon'                  => '圖示',
    'uri'                   => '路徑',
    'operation_log'         => '操作記錄',
    'parent_select_error'   => '父級選擇錯誤',
    'pagination'            => [
        'range' => '從 :first 到 :last ，總共 :total 條',
    ],
    'role'                  => '角色',
    'permission'            => '權限',
    'route'                 => '路由',
    'confirm'               => '確認',
    'cancel'                => '取消',
    'http'                  => [
        'method' => 'HTTP方法',
        'path'   => 'HTTP路徑',
    ],
    'all_methods_if_empty'  => '為空默認為所有方法',
    'all'                   => '全部',
    'current_page'          => '現在頁面',
    'selected_rows'         => '選擇的行',
    'upload'                => '上傳',
    'new_folder'            => '新建資料夾',
    'time'                  => '時間',
    'size'                  => '大小',
    'listbox'               => [
        'text_total'         => '總共 {0} 項',
        'text_empty'         => '空列表',
        'filtered'           => '{0} / {1}',
        'filter_clear'       => '顯示全部',
        'filter_placeholder' => '過濾',
    ],
    'grid_items_selected'   => '{n} 已被選取',

    'menu_titles'           => [],
    'prev'                  => '上一步',
    'next'                  => '下一步',
    'quick_create'          => '快速創建',

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
        'failed'   => '使用者名稱或密碼錯誤',
        'throttle' => '嘗試登入太多次，請在 :seconds 秒後再試。',
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
        'alpha_unicode'      => ':attribute 只能以字母組成。',
        'alpha_dash_unicode' => ':attribute 只能以字母、數字、連接線(-)及底線(_)組成。',
        'alpha_num_unicode'  => ':attribute 只能以字母及數字組成。',
    ],
];
