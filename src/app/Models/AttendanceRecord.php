<?php

namespace App\Models;

use App\Constants\Attendance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * 勤怠記録モデル
 * @property int $id
 * @property int $user_id
 * @property \Carbon\Carbon $date
 * @property string $clock_in_time
 * @property string $clock_out_time
 * @property string $work_state
 */
class AttendanceRecord extends Model
{
    use HasFactory;

    /**
     * フィルター可能な属性
     */
    protected $fillable = [
        'user_id',
        'date',
        'clock_in_time',
        'clock_out_time',
        'work_state',
        'approval_status',
        'applied_at',
        'remark',
        'total_work_time',
        'break_total_time',
        'clock_in_time_correction',
        'clock_out_time_correction',
        'correction_reason',
    ];

    /**
     * キャスト
     */
    protected $casts = [
        'date' => 'date',
        'applied_at' => 'datetime',
    ];

    /**
     * ユーザーとのリレーション
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 休憩記録とのリレーション
     * @return HasMany
     */
    public function breakRecords(): HasMany
    {
        return $this->hasMany(BreakRecord::class);
    }

    /**
     * 勤務状態の表示名を取得
     * @return string
     */
    public function getWorkStateDisplayAttribute(): string
    {
        return Attendance::WORK_STATES[$this->work_state] ?? $this->work_state;
    }

    /**
     * 承認ステータスの表示名を取得
     * @return string
     */
    public function getApprovalStatusDisplayAttribute(): string
    {
        return Attendance::APPROVAL_STATUSES[$this->approval_status] ?? $this->approval_status;
    }

    /**
     * 今日の勤怠記録を取得
     * @param int $userId
     * @return self|null
     */
    public static function getTodayRecord(int $userId): ?self
    {
        return self::where('user_id', $userId)
            ->where('date', today())
            ->first();
    }

    /**
     * 現在の勤務状態を取得
     * @param int $userId
     * @return string
     */
    public static function getCurrentWorkState(int $userId): string
    {
        $record = self::getTodayRecord($userId);

        if (!$record) {
            return 'BEFORE_WORK';
        }

        return $record->work_state;
    }

    /**
     * 出勤処理
     * @param int $userId
     * @return array
     */
    public static function clockIn(int $userId): array
    {
        $todayRecord = self::getTodayRecord($userId);

        // 既に勤怠記録がある場合はエラー
        if ($todayRecord) {
            if ($todayRecord->work_state === 'AFTER_LEAVE') {
                return ['success' => false, 'error' => '本日は既に退勤済みです'];
            } else {
                return ['success' => false, 'error' => '本日は既に出勤済みです'];
            }
        }

        self::create([
            'user_id' => $userId,
            'date' => today(),
            'clock_in_time' => now()->format('H:i'),
            'work_state' => 'WORKING',
        ]);

        return ['success' => true, 'message' => '出勤しました'];
    }

    /**
     * 退勤処理
     * @return array
     */
    public function clockOut(): array
    {
        // 出勤していない場合はエラー
        if (!$this->exists) {
            return ['success' => false, 'error' => '出勤していないため退勤できません'];
        }

        // 既に退勤済みの場合はエラー
        if ($this->work_state === 'AFTER_LEAVE') {
            return ['success' => false, 'error' => '本日は既に退勤済みです'];
        }

        // 勤務外の場合はエラー
        if ($this->work_state === 'BEFORE_WORK') {
            return ['success' => false, 'error' => '出勤していないため退勤できません'];
        }

        DB::transaction(function () {
            // 休憩中の場合は休憩を終了
            if ($this->work_state === 'ON_BREAK') {
                $activeBreak = BreakRecord::where('attendance_record_id', $this->id)
                    ->whereNull('end_time')
                    ->first();

                if ($activeBreak) {
                    $activeBreak->update(['end_time' => now()->format('H:i')]);
                }
            }

            $this->update([
                'clock_out_time' => now()->format('H:i'),
                'work_state' => 'AFTER_LEAVE',
            ]);
        });

        return ['success' => true, 'message' => 'お疲れ様でした。'];
    }

    /**
     * 休憩開始処理
     * @return array
     */
    public function startBreak(): array
    {
        // 出勤していない場合はエラー
        if (!$this->exists || $this->work_state !== 'WORKING') {
            return ['success' => false, 'error' => '出勤していないため休憩できません'];
        }

        DB::transaction(function () {
            // 休憩記録を作成
            BreakRecord::create([
                'attendance_record_id' => $this->id,
                'start_time' => now()->format('H:i'),
            ]);

            // 勤務状態を休憩中に変更
            $this->update(['work_state' => 'ON_BREAK']);
        });

        return ['success' => true, 'message' => '休憩を開始しました'];
    }

    /**
     * 休憩終了処理
     * @return array
     */
    public function endBreak(): array
    {
        // 出勤していない場合はエラー
        if (!$this->exists) {
            return ['success' => false, 'error' => '出勤していないため休憩終了できません'];
        }

        // 休憩中でない場合はエラー
        if ($this->work_state !== 'ON_BREAK') {
            return ['success' => false, 'error' => '休憩中ではないため休憩終了できません'];
        }

        DB::transaction(function () {
            // アクティブな休憩記録を終了
            $activeBreak = BreakRecord::where('attendance_record_id', $this->id)
                ->whereNull('end_time')
                ->first();

            if ($activeBreak) {
                $activeBreak->update(['end_time' => now()->format('H:i')]);
            }

            // 勤務状態を出勤中に変更
            $this->update(['work_state' => 'WORKING']);
        });

        return ['success' => true, 'message' => '休憩を終了しました'];
    }
}
