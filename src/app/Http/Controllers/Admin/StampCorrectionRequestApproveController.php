<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * 修正申請承認コントローラー
 */
class StampCorrectionRequestApproveController extends Controller
{
    /**
     * 修正申請詳細画面を表示
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $attendance = AttendanceRecord::with(['user', 'breakRecords'])
            ->findOrFail($id);

        return view('admin.stamp_correction_requests.approve', compact('attendance'));
    }

    /**
     * 修正申請を承認
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, $id)
    {
        $attendance = AttendanceRecord::findOrFail($id);

        $attendance->approve();

        return redirect()->route('stamp_correction_request.list')
            ->with('success', '修正申請を承認しました。');
    }
}
