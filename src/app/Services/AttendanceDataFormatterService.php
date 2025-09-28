<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * 勤怠データ整形サービスクラス
 */
class AttendanceDataFormatterService
{
    /**
     * 月次勤怠データを整形
     * @param \Illuminate\Database\Eloquent\Collection $attendanceRecords
     * @param \Carbon\Carbon $currentMonth
     * @return array
     */
    public function formatMonthlyData($attendanceRecords, $currentMonth)
    {
        $data = [];

        // 指定月の日数を取得
        $daysInMonth = $currentMonth->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $currentMonth->copy()->day($day);
            $dateString = $date->format('Y-m-d');
            $attendance = $attendanceRecords->filter(function ($record) use ($dateString) {
                return $record->date->format('Y-m-d') === $dateString;
            })->first();

            $data[] = [
                'date' => $date,
                'attendance' => $attendance,
                'break_time' => $attendance ? $attendance->calculateBreakTime() : null,
                'total_work_time' => $attendance ? $attendance->calculateTotalWorkTime() : null,
            ];
        }

        return $data;
    }
}
