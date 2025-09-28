<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * 勤怠データサービスクラス
 */
class AttendanceDataService
{
    /**
     * 月次ナビゲーション用の日付データを取得
     * @param string|null $month
     * @return array
     */
    public function getMonthNavigationData($month = null)
    {
        $currentMonth = $month ? Carbon::createFromFormat('Y-m', $month) : now();

        return [
            'currentMonth' => $currentMonth,
            'prevMonth' => $currentMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $currentMonth->copy()->addMonth()->format('Y-m'),
        ];
    }
}
