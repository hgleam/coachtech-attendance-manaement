<?php

namespace App\Helpers;

/**
 * 認証関連のヘルパークラス
 */
class AuthHelper
{
    /**
     * 管理者かどうかを判定
     * @return bool
     */
    public static function isAdmin()
    {
        return session()->has('admin_id');
    }
}
