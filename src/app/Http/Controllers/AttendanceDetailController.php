<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Http\Requests\AttendanceUpdateRequest;

/**
 * 一般ユーザー用勤怠詳細画面コントローラー
 */
class AttendanceDetailController extends Controller
{
    /**
     * 一般ユーザー用勤怠詳細画面を表示
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show($id)
    {
        /** @var \App\Models\AttendanceRecord $attendance */
        $attendance = AttendanceRecord::query()->with(['breakRecords', 'user'])->findOrFail($id);

        // 認可チェック
        if (!$attendance->canAccess()) {
            abort(403, 'この勤怠記録にアクセスする権限がありません。');
        }

        return view('attendances.show', compact('attendance'));
    }

    /**
     * 修正申請を送信（一般ユーザー用）
     * @param AttendanceUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function correction(AttendanceUpdateRequest $request, $id)
    {
        /** @var \App\Models\AttendanceRecord $attendance */
        $attendance = AttendanceRecord::query()->findOrFail($id);

        // 認可チェック
        if (!$attendance->canModify()) {
            abort(403, 'この勤怠記録に修正する権限がありません。');
        }

        // 一般ユーザーの場合の制限チェック
        // 申請済みの場合は修正申請できない
        if ($attendance->isApproved()) {
            return redirect()->back()->with('error', 'この勤怠記録は既に承認済みです。修正申請はできません。');
        }

        // 既に申請中の場合は修正申請できない
        if ($attendance->isPending()) {
            return redirect()->back()->with('error', 'この勤怠記録は既に修正申請中です。承認されるまで新しい修正申請はできません。');
        }

        $attendance->applyCorrection($request, false);

        return redirect()->back()->with('status', '修正申請を送信しました。');
    }
}
