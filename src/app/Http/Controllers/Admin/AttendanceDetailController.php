<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Http\Requests\AttendanceUpdateRequest;
use Illuminate\Http\Request;

/**
 * 管理者用勤怠詳細コントローラー
 */
class AttendanceDetailController extends Controller
{
    /**
     * 管理者用勤怠詳細画面を表示
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show($id)
    {
        $attendance = AttendanceRecord::with(['breakRecords', 'user'])->findOrFail($id);

        return view('attendances.show', compact('attendance'));
    }

    /**
     * 管理者用勤怠修正処理
     * @param AttendanceUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function correction(AttendanceUpdateRequest $request, $id)
    {
        $attendance = AttendanceRecord::findOrFail($id);

        // 管理者は直接修正可能
        $attendance->applyCorrection($request, true);

        return redirect()->back()->with('status', '勤怠情報を修正しました。');
    }
}
