<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

/**
 * 管理者用スタッフ一覧コントローラー
 */
class StaffListController extends Controller
{
    /**
     * スタッフ一覧画面を表示
     */
    public function index()
    {
        // 全一般ユーザーを取得
        /** @var \App\Models\User[] $users */
        $users = User::query()->orderBy('name')->get();

        return view('admin.staff.list', compact('users'));
    }
}
