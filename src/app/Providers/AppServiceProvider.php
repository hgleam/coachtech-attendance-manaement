<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

/**
 * アプリケーションサービスプロバイダ
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * アプリケーションサービスを登録
     * @return void
     */
    public function register()
    {
    }

    /**
     * アプリケーションサービスをブート
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale('ja');
    }
}
