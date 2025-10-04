<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

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
        $users = User::orderBy('name')->get();

        return view('admin.staff.list', compact('users'));
    }
}
