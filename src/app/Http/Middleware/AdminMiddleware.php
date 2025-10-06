<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * 管理者ミドルウェア
 */
class AdminMiddleware
{
    /**
     * リクエストを処理
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}