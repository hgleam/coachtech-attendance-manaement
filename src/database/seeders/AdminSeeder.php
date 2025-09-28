<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;

/**
 * 管理者データシーダー
 */
class AdminSeeder extends Seeder
{
    /**
     * データベースシーダー実行
     */
    public function run(): void
    {
        // 既存の管理者がいる場合はスキップ
        if (Admin::count() > 0) {
            return;
        }

        // 環境変数から管理者情報を取得（デフォルト値あり）
        $adminEmails = explode(',', config('admin.emails'));
        $defaultPassword = config('admin.default_password');

        foreach ($adminEmails as $email) {
            Admin::create([
                'email' => $email,
                'password' => $defaultPassword, // モデルの$castsで自動的にハッシュ化される
            ]);
        }
    }
}