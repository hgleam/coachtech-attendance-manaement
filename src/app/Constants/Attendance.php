<?php

namespace App\Constants;

/**
 * 勤怠管理の定数
 */
class Attendance
{
    /**
     * 勤怠状態の定数
     * BEFORE_WORK: 勤務外
     * WORKING: 出勤中
     * ON_BREAK: 休憩中
     * AFTER_LEAVE: 退勤済
     * @var string
     */
    public const BEFORE_WORK = 'BEFORE_WORK';
    public const WORKING = 'WORKING';
    public const ON_BREAK = 'ON_BREAK';
    public const AFTER_LEAVE = 'AFTER_LEAVE';

    /**
     * 承認ステータスの定数
     * PENDING: 承認待ち
     * APPROVED: 承認済み
     * REJECTED: 却下
     * @var string
     */
    public const PENDING = 'PENDING';
    public const APPROVED = 'APPROVED';
    public const REJECTED = 'REJECTED';

    /**
     * 勤怠状態の表示名を取得
     * @param string $workState
     * @return string
     */
    public static function getWorkStateDisplay(string $workState): string
    {
        return match($workState) {
            self::BEFORE_WORK => '勤務外',
            self::WORKING => '出勤中',
            self::ON_BREAK => '休憩中',
            self::AFTER_LEAVE => '退勤済',
            default => $workState
        };
    }

    /**
     * 承認ステータスの表示名を取得
     * @param string $approvalStatus
     * @return string
     */
    public static function getApprovalStatusDisplay(string $approvalStatus): string
    {
        return match($approvalStatus) {
            self::PENDING => '承認待ち',
            self::APPROVED => '承認済み',
            self::REJECTED => '却下',
            default => $approvalStatus
        };
    }
}
