<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use App\Services\AttendanceDataFormatterService;
use App\Services\AttendanceDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * 勤怠一覧コントローラー
 */
class AttendanceListController extends Controller
{
    /**
     * 勤怠一覧画面を表示
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            // 月パラメータを取得（デフォルトは現在の月）
            $month = $request->get('month', now()->format('Y-m'));

            // 月次ナビゲーション用の日付データを取得
            $attendanceService = new AttendanceDataService();
            $navigationData = $attendanceService->getMonthNavigationData($month);

            // 指定月の勤怠記録を取得
            $attendanceRecords = AttendanceRecord::forUser($user->id)
                ->forMonth($navigationData['currentMonth']->year, $navigationData['currentMonth']->month)
                ->get();

            // 勤怠データを整形
            $formatter = new AttendanceDataFormatterService();
            $attendanceData = $formatter->formatMonthlyData($attendanceRecords, $navigationData['currentMonth']);

            return view('attendances.list', array_merge($navigationData, [
                'attendanceData' => $attendanceData,
            ]));
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
