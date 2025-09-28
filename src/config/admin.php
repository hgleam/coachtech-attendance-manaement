<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 管理者設定
    |--------------------------------------------------------------------------
    |
    | 管理者のデフォルト設定を定義します。
    |
    */

    'emails' => env('ADMIN_EMAILS', 'admin1@example.com,admin2@example.com'),
    'default_password' => env('ADMIN_DEFAULT_PASSWORD', 'password!!'),
];
