<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 管理者または一般ユーザーミドルウェア
 */
class AdminOrUserMiddleware
{
    /**
     * リクエストを処理
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Illuminate\Http\RedirectResponse
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 管理者の場合はメール認証をスキップ
        if (session()->has('admin_id')) {
            return $next($request);
        }

        // 一般ユーザーの場合はメール認証をチェック
        if (auth()->check()) {
            if (!auth()->user()->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }
            return $next($request);
        }

        return redirect()->route('login');
    }
}