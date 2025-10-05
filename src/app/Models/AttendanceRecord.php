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
        return Attendance::getWorkStateDisplay($this->work_state);
    }

    /**
     * 承認ステータスの表示名を取得
     * @return string
     */
    public function getApprovalStatusDisplayAttribute(): string
    {
        return Attendance::getApprovalStatusDisplay($this->approval_status);
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
            return Attendance::BEFORE_WORK;
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
            if ($todayRecord->work_state === Attendance::AFTER_LEAVE) {
                return ['success' => false, 'error' => '本日は既に退勤済みです'];
            } else {
                return ['success' => false, 'error' => '本日は既に出勤済みです'];
            }
        }

        self::create([
            'user_id' => $userId,
            'date' => today(),
            'clock_in_time' => now()->format('H:i'),
            'work_state' => Attendance::WORKING,
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
        if ($this->work_state === Attendance::AFTER_LEAVE) {
            return ['success' => false, 'error' => '本日は既に退勤済みです'];
        }

        // 勤務外の場合はエラー
        if ($this->work_state === Attendance::BEFORE_WORK) {
            return ['success' => false, 'error' => '出勤していないため退勤できません'];
        }

        DB::transaction(function () {
            // 休憩中の場合は休憩を終了
            if ($this->work_state === Attendance::ON_BREAK) {
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
        if (!$this->exists || $this->work_state !== Attendance::WORKING) {
            return ['success' => false, 'error' => '出勤していないため休憩できません'];
        }

        DB::transaction(function () {
            // 休憩記録を作成
            BreakRecord::create([
                'attendance_record_id' => $this->id,
                'start_time' => now()->format('H:i'),
            ]);

            // 勤務状態を休憩中に変更
            $this->update(['work_state' => Attendance::ON_BREAK]);
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
        if ($this->work_state !== Attendance::ON_BREAK) {
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
            $this->update(['work_state' => Attendance::WORKING]);
        });

        return ['success' => true, 'message' => '休憩を終了しました'];
    }

    /**
     * 時間文字列を分に変換
     * @param string $timeString
     * @return int
     */
    public static function parseTimeToMinutes($timeString)
    {
        list($hours, $minutes) = explode(':', $timeString);
        return intval($hours) * 60 + intval($minutes);
    }

    /**
     * 時間の形式を正規化（H:i:s -> H:i）
     * @param string $timeString
     * @return string|null
     */
    public static function normalizeTime($timeString)
    {
        if (empty($timeString)) {
            return null;
        }

        // H:i:s 形式の場合は H:i に変換
        if (preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $timeString)) {
            return substr($timeString, 0, 5);
        }

        // H:i 形式の場合はそのまま返す
        if (preg_match('/^\d{1,2}:\d{2}$/', $timeString)) {
            return $timeString;
        }

        return null;
    }

    /**
     * フォーマットされた出勤時間を取得
     * @return string|null
     */
    public function getFormattedClockInTime()
    {
        return self::normalizeTime($this->clock_in_time);
    }

    /**
     * フォーマットされた退勤時間を取得
     * @return string|null
     */
    public function getFormattedClockOutTime()
    {
        return self::normalizeTime($this->clock_out_time);
    }

    /**
     * 休憩時間を計算
     * @return string
     */
    public function calculateBreakTime()
    {
        try {
            $breakRecords = BreakRecord::where('attendance_record_id', $this->id)->get();

            $totalBreakMinutes = 0;
            foreach ($breakRecords as $break) {
                if ($break->start_time && $break->end_time) {
                    $totalBreakMinutes += $break->start_time->diffInMinutes($break->end_time);
                }
            }

            $hours = intval($totalBreakMinutes / 60);
            $minutes = $totalBreakMinutes % 60;

            return sprintf('%d:%02d', $hours, $minutes);
        } catch (\Exception $e) {
            return '0:00';
        }
    }

    /**
     * 総勤務時間を計算
     * @return string|null
     */
    public function calculateTotalWorkTime()
    {
        try {
            if (!$this->clock_in_time || !$this->clock_out_time) {
                return null;
            }

            // 時間の形式を正規化
            $clockInTime = $this->getFormattedClockInTime();
            $clockOutTime = $this->getFormattedClockOutTime();

            if (!$clockInTime || !$clockOutTime) {
                return null;
            }

            $clockIn = \Carbon\Carbon::createFromFormat('H:i', $clockInTime);
            $clockOut = \Carbon\Carbon::createFromFormat('H:i', $clockOutTime);

            $totalMinutes = $clockOut->diffInMinutes($clockIn);

            // 休憩時間を差し引く
            $breakTime = $this->calculateBreakTime();
            if ($breakTime) {
                $breakMinutes = self::parseTimeToMinutes($breakTime);
                $totalMinutes -= $breakMinutes;
            }

            $hours = intval($totalMinutes / 60);
            $minutes = $totalMinutes % 60;

            return sprintf('%d:%02d', $hours, $minutes);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * 指定ユーザーの勤怠記録を取得するスコープ
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * 指定月の勤怠記録を取得するスコープ
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $year
     * @param int $month
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)
                     ->whereMonth('date', $month)
                     ->orderBy('date');
    }

    /**
     * 承認済みかどうかを判定
     * @return bool
     */
    public function isApproved()
    {
        return $this->approval_status === Attendance::APPROVED;
    }

    /**
     * 申請中かどうかを判定
     * @return bool
     */
    public function isPending()
    {
        return $this->approval_status === Attendance::PENDING;
    }

    /**
     * 編集可能かどうかを判定（管理者用）
     * @return bool
     */
    public function canEditAsAdmin()
    {
        return !$this->isApproved();
    }

    /**
     * 編集可能かどうかを判定（一般ユーザー用）
     * @return bool
     */
    public function canEditAsUser()
    {
        // 修正申請中は編集不可
        if ($this->isPending()) {
            return false;
        }

        // 勤務中（退勤していない）場合は編集不可
        if ($this->work_state !== Attendance::AFTER_LEAVE) {
            return false;
        }

        return true;
    }

    /**
     * 編集可能かどうかを判定（現在のユーザーに応じて）
     * @return bool
     */
    public function canEdit()
    {
        if (\App\Helpers\AuthHelper::isAdmin()) {
            return $this->canEditAsAdmin();
        } else {
            return $this->canEditAsUser();
        }
    }

    /**
     * 現在のユーザーがこの勤怠記録にアクセス可能か判定
     * @return bool
     */
    public function canAccess()
    {
        if (\App\Helpers\AuthHelper::isAdmin()) {
            return true; // 管理者は全勤怠記録にアクセス可能
        }

        return $this->user_id === \Illuminate\Support\Facades\Auth::id(); // 一般ユーザーは自分の勤怠記録のみ
    }

    /**
     * 現在のユーザーがこの勤怠記録を修正可能か判定
     * @return bool
     */
    public function canModify()
    {
        if (\App\Helpers\AuthHelper::isAdmin()) {
            return true; // 管理者は全勤怠記録を修正可能
        }

        // 一般ユーザーは自分の勤怠記録のみ
        if ($this->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            return false;
        }

        // 退勤していない勤怠記録は修正不可
        if ($this->work_state !== Attendance::AFTER_LEAVE) {
            return false;
        }

        return true;
    }

    /**
     * 修正申請を適用
     * @param \Illuminate\Http\Request $request
     * @param bool $isAdmin
     * @return void
     */
    public function applyCorrection($request, $isAdmin)
    {
        DB::transaction(function () use ($request, $isAdmin) {
            // 勤怠記録の更新
            $this->updateAttendanceData($request, $isAdmin);

            // 休憩記録の更新
            $this->updateBreakRecords($request);
        });
    }

    /**
     * 勤怠記録データを更新
     * @param \Illuminate\Http\Request $request
     * @param bool $isAdmin
     * @return void
     */
    private function updateAttendanceData($request, $isAdmin)
    {
        $updateData = [
            'clock_in_time' => $request->clock_in_time,
            'clock_out_time' => $request->clock_out_time,
            'remark' => $request->remark,
            'applied_at' => now()
        ];

        if ($isAdmin) {
            $updateData['approval_status'] = Attendance::APPROVED;
        } else {
            $updateData['approval_status'] = Attendance::PENDING;
        }

        $this->update($updateData);
    }

    /**
     * 休憩記録を更新
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    private function updateBreakRecords($request)
    {
        // 既存の休憩記録を削除
        $this->breakRecords()->delete();

        // 新しい休憩記録を作成
        $breakStartTimes = $request->break_start_time ?? [];
        $breakEndTimes = $request->break_end_time ?? [];

        for ($i = 0; $i < count($breakStartTimes); $i++) {
            $startTime = $breakStartTimes[$i] ?? null;
            $endTime = $breakEndTimes[$i] ?? null;

            // 空の休憩時間はスキップ
            if (empty($startTime) && empty($endTime)) {
                continue;
            }

            // 開始時間または終了時間のどちらかが入力されている場合は作成
            if (!empty($startTime) || !empty($endTime)) {
                \App\Models\BreakRecord::create([
                    'attendance_record_id' => $this->id,
                    'start_time' => $startTime ?: null,
                    'end_time' => $endTime ?: null
                ]);
            }
        }
    }

    /**
     * 修正申請を承認
     * @return void
     */
    public function approve()
    {
        $this->update([
            'approval_status' => 'APPROVED'
        ]);
    }
}
