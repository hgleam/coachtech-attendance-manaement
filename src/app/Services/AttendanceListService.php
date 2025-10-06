<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\User;

/**
 * 勤怠一覧サービス
 */
class AttendanceListService
{
    /**
     * 勤怠一覧データを取得
     * @param User $user
     * @param string $month
     * @param string|null $errorMessage
     * @return array<string, mixed>
     */
    public function getAttendanceListData(User $user, string $month, ?string $errorMessage = null): array
    {
        $attendanceService = new AttendanceDataService();
        $navigationData = $attendanceService->getMonthNavigationData($month);

        /** @var \App\Models\AttendanceRecord[] $attendanceRecords */
        $attendanceRecords = AttendanceRecord::query()->where('user_id', $user->getKey())
            ->whereYear('date', $navigationData['currentMonth']->year)
            ->whereMonth('date', $navigationData['currentMonth']->month)
            ->orderBy('date')
            ->get();

        $formatter = new AttendanceDataFormatterService();
        $attendanceData = $formatter->formatMonthlyData($attendanceRecords, $navigationData['currentMonth']);

        return array_merge($navigationData, [
            'attendanceData' => $attendanceData,
            'error' => $errorMessage,
        ]);
    }
}
