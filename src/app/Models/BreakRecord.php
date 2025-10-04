<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 休憩記録モデル
 */
class BreakRecord extends Model
{
    use HasFactory;

    /**
     * フィルター対象
     */
    protected $fillable = [
        'attendance_record_id',
        'start_time',
        'end_time',
    ];

    /**
     * キャスト
     */
    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    /**
     * 勤怠記録とのリレーション
     * @return BelongsTo
     */
    public function attendanceRecord(): BelongsTo
    {
        return $this->belongsTo(AttendanceRecord::class);
    }

    /**
     * 休憩時間を計算
     * @return int|null
     */
    public function getBreakDurationAttribute(): ?int
    {
        if (!$this->start_time || !$this->end_time) {
            return null;
        }

        return $this->start_time->diffInMinutes($this->end_time);
    }
}
