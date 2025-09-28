<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 管理者テーブル作成マイグレーション
 */
return new class extends Migration
{
    /**
     * マイグレーション実行
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * マイグレーションを逆に実行
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
