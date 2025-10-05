<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

/**
 * スタッフ勤怠CSVサービスクラス
 */
class StaffAttendanceCsvService
{
    /**
     * CSVデータを生成
     * @param array $attendanceMap
     * @param Carbon $currentMonth
     * @param User $user
     * @return string
     */
    public function generateCsvData($attendanceMap, $currentMonth, $user)
    {
        $output = fopen('php://temp', 'r+');

        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, [
            '日付',
            '曜日',
            '出勤時間',
            '退勤時間',
            '休憩時間',
            '総労働時間',
            '備考'
        ]);

        $daysInMonth = $currentMonth->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $currentMonth->copy()->day($day);
            $dateKey = $date->format('Y-m-d');
            $attendance = $attendanceMap[$dateKey] ?? null;

            $weekday = ['日', '月', '火', '水', '木', '金', '土'][$date->dayOfWeek];

            if ($attendance) {
                $clockInTime = $attendance->clock_in_time ? substr($attendance->clock_in_time, 0, 5) : '';
                $clockOutTime = $attendance->clock_out_time ? substr($attendance->clock_out_time, 0, 5) : '';
                $breakTime = $attendance->calculateBreakTime();
                $totalWorkTime = $attendance->calculateTotalWorkTime();
                $remark = $attendance->remark ?? '';
            } else {
                $clockInTime = '';
                $clockOutTime = '';
                $breakTime = '';
                $totalWorkTime = '';
                $remark = '';
            }

            fputcsv($output, [
                $date->format('Y-m-d'),
                $weekday,
                $clockInTime,
                $clockOutTime,
                $breakTime,
                $totalWorkTime,
                $remark
            ]);
        }

        fputcsv($output, []);
        fputcsv($output, ['月合計', '', '', '', $this->calculateMonthlyBreakTime($attendanceMap), $this->calculateMonthlyWorkTime($attendanceMap), '']);

        rewind($output);
        $csvData = stream_get_contents($output);
        fclose($output);

        return $csvData;
    }

    /**
     * 月合計休憩時間を計算
     * @param array $attendanceMap
     * @return string
     */
    private function calculateMonthlyBreakTime($attendanceMap)
    {
        $totalBreakMinutes = 0;

        foreach ($attendanceMap as $attendance) {
            if ($attendance->breakRecords) {
                foreach ($attendance->breakRecords as $break) {
                    if ($break->start_time && $break->end_time) {
                        $startTime = is_string($break->start_time) ? Carbon::createFromFormat('H:i:s', $break->start_time) : $break->start_time;
                        $endTime = is_string($break->end_time) ? Carbon::createFromFormat('H:i:s', $break->end_time) : $break->end_time;
                        $totalBreakMinutes += $endTime->diffInMinutes($startTime);
                    }
                }
            }
        }

        if ($totalBreakMinutes === 0) {
            return '';
        }

        $hours = intval($totalBreakMinutes / 60);
        $minutes = $totalBreakMinutes % 60;

        return sprintf('%d:%02d', $hours, $minutes);
    }

    /**
     * 月合計労働時間を計算
     * @param array $attendanceMap
     * @return string
     */
    private function calculateMonthlyWorkTime($attendanceMap)
    {
        $totalWorkMinutes = 0;

        foreach ($attendanceMap as $attendance) {
            if ($attendance->clock_in_time && $attendance->clock_out_time) {
                $clockIn = is_string($attendance->clock_in_time) ? Carbon::createFromFormat('H:i:s', $attendance->clock_in_time) : $attendance->clock_in_time;
                $clockOut = is_string($attendance->clock_out_time) ? Carbon::createFromFormat('H:i:s', $attendance->clock_out_time) : $attendance->clock_out_time;

                $totalMinutes = $clockOut->diffInMinutes($clockIn);

                // 休憩時間を差し引く
                $breakMinutes = 0;
                if ($attendance->breakRecords) {
                    foreach ($attendance->breakRecords as $break) {
                        if ($break->start_time && $break->end_time) {
                            $startTime = is_string($break->start_time) ? Carbon::createFromFormat('H:i:s', $break->start_time) : $break->start_time;
                            $endTime = is_string($break->end_time) ? Carbon::createFromFormat('H:i:s', $break->end_time) : $break->end_time;
                            $breakMinutes += $endTime->diffInMinutes($startTime);
                        }
                    }
                }

                $workMinutes = $totalMinutes - $breakMinutes;
                $totalWorkMinutes += max(0, $workMinutes);
            }
        }

        if ($totalWorkMinutes === 0) {
            return '';
        }

        $hours = intval($totalWorkMinutes / 60);
        $minutes = $totalWorkMinutes % 60;

        return sprintf('%d:%02d', $hours, $minutes);
    }
}
