<?php

namespace App\Constants;

/**
 * 勤怠管理の定数
 */
class Attendance
{
    /**
     * 勤怠状態
     */
    const WORK_STATES = [
        'BEFORE_WORK' => '出勤前',
        'AFTER_WORK' => '出勤後',
        'ON_BREAK' => '休憩中',
        'AFTER_LEAVE' => '退勤後'
    ];

    /**
     * 勤怠承認ステータス
     */
    const APPROVAL_STATUSES = [
        'PENDING' => '承認待ち',
        'APPROVED' => '承認済み',
        'REJECTED' => '却下'
    ];
}
