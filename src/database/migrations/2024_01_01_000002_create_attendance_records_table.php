<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\Attendance;

/**
 * 勤怠記録テーブルのマイグレーション
 */
return new class extends Migration
{
    /**
     * マイグレーションを実行
     */
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('clock_in_time');
            $table->time('clock_out_time')->nullable();
            $table->enum('work_state', [Attendance::BEFORE_WORK, Attendance::WORKING, Attendance::ON_BREAK, Attendance::AFTER_LEAVE]);
            $table->enum('approval_status', [Attendance::PENDING, Attendance::APPROVED, Attendance::REJECTED])->nullable();
            $table->timestamp('applied_at')->nullable()->comment('修正申請日時');
            $table->text('remark')->nullable();
            $table->time('total_work_time')->nullable()->comment('総勤務時間');
            $table->time('break_total_time')->nullable()->comment('休憩時間合計');
            $table->timestamps();

            // インデックス
            $table->index(['user_id', 'date']);
            $table->index('date');
            $table->index('approval_status');
        });
    }

    /**
     * マイグレーションを削除
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
