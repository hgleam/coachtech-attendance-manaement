<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 一般ユーザーがログインしているか、管理者がログインしているかチェック
        if (auth()->check() || (session()->has('admin_id') && session()->has('admin_name'))) {
            return $next($request);
        }

        // どちらもログインしていない場合は一般ユーザーのログイン画面にリダイレクト
        return redirect()->route('login');
    }
}