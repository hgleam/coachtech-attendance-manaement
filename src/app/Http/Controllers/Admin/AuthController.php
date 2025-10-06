<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Models\Admin;

/**
 * 管理者認証コントローラー
 */
class AuthController extends Controller
{
    /**
     * 管理者ログイン画面を表示
     * @return \Illuminate\Contracts\View\View
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * 管理者ログイン処理
     * @param AdminLoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(AdminLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // 管理者の認証
        $admin = Admin::authenticate($credentials['email'], $credentials['password']);

        if (!$admin) {
            return redirect()->back()
                ->withErrors(['email' => 'ログイン情報が登録されていません'])
                ->withInput($request->except('password'));
        }

        session(['admin_id' => $admin->getKey()]);

        return redirect()->route('admin.attendance.list');
    }

    /**
     * 管理者ログアウト処理
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        session()->forget(['admin_id']);
        return redirect()->route('admin.login');
    }
}