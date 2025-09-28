<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 管理者ミドルウェア
 */
class AdminMiddleware
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
        // セッションに管理者情報があるかチェック
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}