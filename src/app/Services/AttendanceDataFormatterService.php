<?php

namespace App\Services;


/**
 * 勤怠データ整形サービスクラス
 */
class AttendanceDataFormatterService
{
    /**
     * 月次勤怠データを整形
     * @param \App\Models\AttendanceRecord[] $attendanceRecords
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
            $attendance = collect($attendanceRecords)->filter(function ($record) use ($dateString) {
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
     * @param \Illuminate\Support\Collection $attendanceRecords
     * @param \App\Models\User[] $allUsers
     * @return array
     */
    public function formatAdminAttendanceData($attendanceRecords, $allUsers)
    {
        $data = [];

        foreach ($allUsers as $user) {
            $attendance = $attendanceRecords->where('user_id', $user->getKey())->first();

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
