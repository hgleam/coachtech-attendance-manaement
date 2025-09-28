<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 休憩記録テーブルのマイグレーション
 */
return new class extends Migration
{
    /**
     * マイグレーションを実行
     */
    public function up(): void
    {
        Schema::create('break_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_record_id')->constrained()->onDelete('cascade');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->timestamps();

            // インデックス
            $table->index('attendance_record_id');
        });
    }

    /**
     * マイグレーションを削除
     */
    public function down(): void
    {
        Schema::dropIfExists('break_records');
    }
};
