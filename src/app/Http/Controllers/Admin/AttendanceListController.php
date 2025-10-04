<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\User;
use App\Services\AttendanceDataFormatterService;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * 管理者用勤怠一覧画面コントローラー
 */
class AttendanceListController extends Controller
{
    /**
     * 管理者用勤怠一覧画面を表示
     */
    public function index(Request $request)
    {
        try {
            // 日付パラメータを取得（デフォルトは今日）
            $date = $request->get('date', now()->format('Y-m-d'));
            $currentDate = Carbon::createFromFormat('Y-m-d', $date);

            // 前日・翌日のリンク用
            $prevDate = $currentDate->copy()->subDay()->format('Y-m-d');
            $nextDate = $currentDate->copy()->addDay()->format('Y-m-d');

            // 指定日の全ユーザーの勤怠記録を取得
            $attendanceRecords = AttendanceRecord::with(['user', 'breakRecords'])
                ->whereDate('date', $currentDate)
                ->orderBy('user_id')
                ->get();

            // 全ユーザーを取得（勤怠記録がないユーザーも含める）
            $allUsers = User::orderBy('name')->get();

            // 勤怠データを整形
            $formatter = new AttendanceDataFormatterService();
            $attendanceData = $formatter->formatAdminAttendanceData($attendanceRecords, $allUsers);

            return view('admin.attendances.list', compact(
                'attendanceData',
                'currentDate',
                'prevDate',
                'nextDate'
            ));
        } catch (\Exception $e) {
            throw $e;
        }
    }
}