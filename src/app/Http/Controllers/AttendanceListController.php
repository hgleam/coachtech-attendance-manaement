<?php

namespace App\Http\Controllers;

use App\Services\AttendanceListService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();
        $month = $request->get('month', now()->format('Y-m'));

        try {
            $service = new AttendanceListService();
            $data = $service->getAttendanceListData($user, $month);
            return view('attendances.list', $data);
        } catch (\InvalidArgumentException $e) {
            // 無効な月の場合はエラーメッセージを表示して現在の月で表示
            $service = new AttendanceListService();
            $data = $service->getAttendanceListData($user, now()->format('Y-m'), $e->getMessage());
            return view('attendances.list', $data);
        }
    }

}
