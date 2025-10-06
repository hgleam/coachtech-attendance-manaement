<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * 管理者または一般ユーザーミドルウェア
 */
class AdminOrUserMiddleware
{
    /**
     * リクエストを処理
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        // 管理者の場合はメール認証をスキップ
        if (session()->has('admin_id')) {
            return $next($request);
        }

        // 一般ユーザーの場合はメール認証をチェック
        if (auth()->check()) {
            /** @var \App\Models\User $user */
            $user = auth()->user();
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }
            return $next($request);
        }

        return redirect()->route('login');
    }
}
