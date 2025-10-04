<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;

/**
 * 管理者用勤怠詳細コントローラー
 */
class AttendanceDetailController extends Controller
{
    /**
     * 管理者用勤怠詳細画面を表示
     */
    public function show($id)
    {
        try {
            $attendance = AttendanceRecord::with(['user', 'breakRecords'])
                ->findOrFail($id);

            return view('admin.attendances.detail', compact('attendance'));
        } catch (\Exception $e) {
            return redirect()->route('admin.attendance.list')
                ->with('error', '勤怠記録が見つかりません。');
        }
    }
}
