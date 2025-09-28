<?php

namespace App\Http\Controllers;

use App\Constants\Attendance;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * 勤怠コントローラー
 */
class AttendanceController extends Controller
{
    /**
     * 勤怠登録画面を表示
     */
    public function index()
    {
        $user = Auth::user();
        $currentWorkState = AttendanceRecord::getCurrentWorkState($user->id);
        $todayRecord = AttendanceRecord::getTodayRecord($user->id);

        $data = [
            'currentWorkState' => $currentWorkState,
            'workStateDisplay' => Attendance::WORK_STATES[$currentWorkState],
            'todayRecord' => $todayRecord,
            'currentTime' => now(),
        ];

        return view('attendances.index', $data);
    }

    /**
     * 出勤処理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clockIn(Request $request)
    {
        $user = Auth::user();
        $result = AttendanceRecord::clockIn($user->id);

        if (!$result['success']) {
            return back()->withErrors(['attendance' => $result['error']]);
        }

        return redirect('/attendance')->with('status', $result['message']);
    }

    /**
     * 退勤処理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clockOut(Request $request)
    {
        $user = Auth::user();
        $todayRecord = AttendanceRecord::getTodayRecord($user->id);

        if (!$todayRecord) {
            return back()->withErrors(['attendance' => '出勤していないため退勤できません']);
        }

        $result = $todayRecord->clockOut();

        if (!$result['success']) {
            return back()->withErrors(['attendance' => $result['error']]);
        }

        return redirect('/attendance')->with('status', $result['message']);
    }

    /**
     * 休憩開始処理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function breakStart(Request $request)
    {
        $user = Auth::user();
        $todayRecord = AttendanceRecord::getTodayRecord($user->id);

        if (!$todayRecord) {
            return back()->withErrors(['attendance' => '出勤していないため休憩できません']);
        }

        $result = $todayRecord->startBreak();

        if (!$result['success']) {
            return back()->withErrors(['attendance' => $result['error']]);
        }

        return redirect('/attendance')->with('status', $result['message']);
    }

    /**
     * 休憩終了処理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function breakEnd(Request $request)
    {
        $user = Auth::user();
        $todayRecord = AttendanceRecord::getTodayRecord($user->id);

        if (!$todayRecord) {
            return back()->withErrors(['attendance' => '出勤していないため休憩終了できません']);
        }

        $result = $todayRecord->endBreak();

        if (!$result['success']) {
            return back()->withErrors(['attendance' => $result['error']]);
        }

        return redirect('/attendance')->with('status', $result['message']);
    }
}
