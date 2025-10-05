<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\User;
use App\Services\AttendanceDataFormatterService;
use App\Services\AttendanceDataService;

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
     * @return array
     */
    public function getAttendanceListData(User $user, string $month, ?string $errorMessage = null): array
    {
        $attendanceService = new AttendanceDataService();
        $navigationData = $attendanceService->getMonthNavigationData($month);

        $attendanceRecords = AttendanceRecord::forUser($user->id)
            ->forMonth($navigationData['currentMonth']->year, $navigationData['currentMonth']->month)
            ->get();

        $formatter = new AttendanceDataFormatterService();
        $attendanceData = $formatter->formatMonthlyData($attendanceRecords, $navigationData['currentMonth']);

        return array_merge($navigationData, [
            'attendanceData' => $attendanceData,
            'error' => $errorMessage,
        ]);
    }
}
