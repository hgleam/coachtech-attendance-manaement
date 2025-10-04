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

    /**
     * 管理者用勤怠データを整形
     * @param \Illuminate\Database\Eloquent\Collection $attendanceRecords
     * @param \Illuminate\Database\Eloquent\Collection $allUsers
     * @return array
     */
    public function formatAdminAttendanceData($attendanceRecords, $allUsers)
    {
        $data = [];

        foreach ($allUsers as $user) {
            $attendance = $attendanceRecords->where('user_id', $user->id)->first();

            $data[] = [
                'user' => $user,
                'attendance' => $attendance,
                'clock_in_time' => $attendance ? $attendance->clock_in_time : null,
                'clock_out_time' => $attendance ? $attendance->clock_out_time : null,
                'break_time' => $attendance ? $attendance->calculateBreakTime() : null,
                'total_work_time' => $attendance ? $attendance->calculateTotalWorkTime() : null,
            ];
        }

        return $data;
    }
}
