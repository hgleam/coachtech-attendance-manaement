<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * 申請一覧画面コントローラー
 */
class StampCorrectionRequestListController extends Controller
{
    /**
     * 申請一覧画面を表示
     */
    public function index(Request $request)
    {
        // 管理者かどうかを判定
        $isAdmin = session()->has('admin_id');

        if ($isAdmin) {
            // 管理者の場合：全ユーザーの申請を表示
            $baseQuery = AttendanceRecord::with(['user', 'breakRecords']);
        } else {
            // 一般ユーザーの場合：自分の申請のみ表示
            $userId = Auth::id();
            $baseQuery = AttendanceRecord::where('user_id', $userId)->with(['user', 'breakRecords']);
        }

        // 承認待ちの申請を取得
        $pendingRequests = (clone $baseQuery)->where('approval_status', 'PENDING')
            ->orderBy('applied_at', 'desc')
            ->get();

        // 承認済みの申請を取得
        $approvedRequests = (clone $baseQuery)->where('approval_status', 'APPROVED')
            ->orderBy('applied_at', 'desc')
            ->get();

        $viewName = $isAdmin ? 'admin.stamp_correction_requests.list' : 'stamp_correction_requests.list';

        return view($viewName, compact('pendingRequests', 'approvedRequests', 'isAdmin'));
    }
}