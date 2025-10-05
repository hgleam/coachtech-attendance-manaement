<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;

/**
 * スタッフ勤怠データサービスクラス
 */
class StaffAttendanceDataService
{
    /**
     * 指定ユーザーの月次勤怠データを取得
     * @param int $userId
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getMonthlyAttendanceData($userId, $year, $month)
    {
        $currentMonth = Carbon::create($year, $month, 1);

        // 指定された年月の勤怠記録を取得
        $attendanceRecords = AttendanceRecord::where('user_id', $userId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->with('breakRecords')
            ->orderBy('date')
            ->get();

        // 日付をキーとしたマップを作成
        $attendanceMap = $this->createAttendanceMap($attendanceRecords);

        return [
            'currentMonth' => $currentMonth,
            'prevMonth' => $currentMonth->copy()->subMonth(),
            'nextMonth' => $currentMonth->copy()->addMonth(),
            'attendanceMap' => $attendanceMap
        ];
    }

    /**
     * 勤怠データをフォーマット
     * @param array $attendanceMap
     * @param Carbon $currentMonth
     * @return array
     */
    public function formatAttendanceData($attendanceMap, $currentMonth)
    {
        $data = [];

        $daysInMonth = $currentMonth->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $currentMonth->copy()->day($day);
            $dateKey = $date->format('Y-m-d');
            $attendance = $attendanceMap[$dateKey] ?? null;

            $data[] = [
                'date' => $date,
                'attendance' => $attendance,
                'clock_in_time' => $attendance ? substr($attendance->clock_in_time, 0, 5) : '',
                'clock_out_time' => $attendance ? substr($attendance->clock_out_time, 0, 5) : '',
                'break_time' => $attendance ? $attendance->calculateBreakTime() : '',
                'total_work_time' => $attendance ? $attendance->calculateTotalWorkTime() : '',
            ];
        }

        return $data;
    }

    /**
     * 勤怠記録のマップを作成
     * @param \Illuminate\Database\Eloquent\Collection $attendanceRecords
     * @return array
     */
    private function createAttendanceMap($attendanceRecords)
    {
        $attendanceMap = [];
        foreach ($attendanceRecords as $record) {
            if ($record->date instanceof \Carbon\Carbon) {
                $dateKey = $record->date->format('Y-m-d');
            } else {
                $dateKey = \Carbon\Carbon::parse($record->date)->format('Y-m-d');
            }
            $attendanceMap[$dateKey] = $record;
        }

        return $attendanceMap;
    }
}
