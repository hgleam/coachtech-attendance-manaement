<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffAttendanceRequest;
use App\Models\User;
use App\Services\StaffAttendanceDataService;
use App\Services\StaffAttendanceCsvService;

/**
 * 管理者用スタッフ月次勤怠コントローラー
 */
class StaffAttendanceController extends Controller
{
    /**
     * 指定ユーザーの月次勤怠画面を表示
     * @param StaffAttendanceRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(StaffAttendanceRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $yearMonth = $request->getYearMonth();

        $dataService = new StaffAttendanceDataService();
        $data = $dataService->getMonthlyAttendanceData($id, $yearMonth['year'], $yearMonth['month']);
        $attendanceData = $dataService->formatAttendanceData($data['attendanceMap'], $data['currentMonth']);

        return view('admin.attendances.staff', [
            'user' => $user,
            'currentMonth' => $data['currentMonth'],
            'prevMonth' => $data['prevMonth'],
            'nextMonth' => $data['nextMonth'],
            'attendanceData' => $attendanceData
        ]);
    }

    /**
     * 指定ユーザーの月次勤怠データをCSV出力
     * @param StaffAttendanceRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function exportCsv(StaffAttendanceRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $yearMonth = $request->getYearMonth();

        $dataService = new StaffAttendanceDataService();
        $data = $dataService->getMonthlyAttendanceData($id, $yearMonth['year'], $yearMonth['month']);

        $csvService = new StaffAttendanceCsvService();
        $csvData = $csvService->generateCsvData($data['attendanceMap'], $data['currentMonth'], $user);
        $fileName = sprintf('%s_%s_%s.csv', $user->name, $data['currentMonth']->format('Y'), $data['currentMonth']->format('m'));

        return response($csvData, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
