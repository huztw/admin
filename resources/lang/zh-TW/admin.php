<?php

return [
    'action'                => '操作',
    'administrator'         => '管理員',
    'alert'                 => '警告',
    'all'                   => '全部',
    'all_methods_if_empty'  => '為空默認為所有方法',
    'avatar'                => '頭像',
    'back'                  => '返回',
    'back_to_list'          => '返回列表',
    'batch_delete'          => '批次刪除',
    'browse'                => '瀏覽',
    'cancel'                => '取消',
    'captcha'               => '驗證碼',
    'choose'                => '選擇',
    'choose_file'           => '選擇檔案',
    'choose_image'          => '選擇圖片',
    'close'                 => '關閉',
    'collapse'              => '收起',
    'confirm'               => '確認',
    'continue_creating'     => '繼續創建',
    'continue_editing'      => '繼續編輯',
    'create'                => '創建',
    'created_at'            => '建立時間',
    'current_page'          => '現在頁面',
    'delete'                => '刪除',
    'delete_confirm'        => '確認刪除？',
    'delete_failed'         => '刪除失敗！',
    'delete_succeeded'      => '刪除成功！',
    'deny'                  => '權限不足',
    'description'           => '簡介',
    'detail'                => '詳細',
    'edit'                  => '編輯',
    'entries'               => '條',
    'expand'                => '展開',
    'export'                => '匯出',
    'failed'                => '失敗',
    'filter'                => '篩選',
    'grid_items_selected'   => '{n} 已被選取',
    'home'                  => '首頁',
    'http'                  => [
        'method' => 'HTTP方法',
        'path'   => 'HTTP路徑',
        'status' => [
            401 => '權限不足',
            403 => '無法瀏覽',
            404 => '頁面不存在',
            419 => '頁面逾時',
            423 => '頁面不存在',
            429 => '請求次數過多',
            500 => '伺服器錯誤',
            503 => '暫時不提供服務',
        ],
    ],
    'icon'                  => '圖示',
    'input'                 => '輸入',
    'list'                  => '列表',
    'listbox'               => [
        'filtered'           => '{0} / {1}',
        'filter_clear'       => '顯示全部',
        'filter_placeholder' => '過濾',
        'text_empty'         => '空列表',
        'text_total'         => '總共 {0} 項',
    ],
    'login'                 => '登入',
    'login_failed'          => '登入失敗！',
    'login_successful'      => '成功登入！',
    'logout'                => '登出',
    'menu'                  => '目錄',
    'menu_titles'           => [],
    'more'                  => '更多',
    'name'                  => '名稱',
    'new'                   => '新增',
    'new_folder'            => '新建資料夾',
    'next'                  => '下一步',
    'online'                => '在線',
    'operation_log'         => '操作記錄',
    'order'                 => '排序',
    'pagination'            => [
        'range' => '從 :first 到 :last ，總共 :total 條',
    ],
    'parent_id'             => '父目錄',
    'parent_select_error'   => '父級選擇錯誤',
    'password'              => '密碼',
    'password_confirmation' => '確認密碼',
    'permission'            => '權限',
    'permissions'           => '權限',
    'prev'                  => '上一步',
    'quick_create'          => '快速創建',
    'refresh'               => '重新整理',
    'refresh_succeeded'     => '成功重新整理！',
    'register'              => '註冊',
    'remember_me'           => '記住我',
    'remove'                => '移除',
    'reset'                 => '重置',
    'roles'                 => '角色',
    'role'                  => '角色',
    'route'                 => '路由',
    'save'                  => '儲存',
    'save_succeeded'        => '儲存成功！',
    'search'                => '搜尋',
    'selected_rows'         => '選擇的行',
    'setting'               => '設置',
    'show'                  => '顯示',
    'size'                  => '大小',
    'slug'                  => '標誌',
    'submit'                => '送出',
    'succeeded'             => '成功',
    'time'                  => '時間',
    'title'                 => '標題',
    'update_succeeded'      => '更新成功！',
    'updated_at'            => '更新時間',
    'upload'                => '上傳',
    'uri'                   => '路徑',
    'username'              => '用戶名',
    'user_setting'          => '用戶設置',
    'view'                  => '查看',

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
        'alpha_dash_unicode' => ':attribute 只能以字母、數字、連接線(-)及底線(_)組成。',
        'alpha_num_unicode'  => ':attribute 只能以字母及數字組成。',
        'alpha_unicode'      => ':attribute 只能以字母組成。',
    ],
];
