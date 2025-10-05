<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('attendance.index');
    }
    return redirect()->route('login');
});

// 仮ルーティング
// 一般ユーザー
Route::get('/register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])
    ->name('register');

Route::post('/register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);

Route::get('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])
    ->name('login');

Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);

Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

// メール認証関連のルート
Route::get('/email/verify', [App\Http\Controllers\EmailVerificationController::class, 'notice'])
    ->middleware('auth')
    ->name('verification.notice');

Route::post('/email/verification-notification', [App\Http\Controllers\EmailVerificationController::class, 'send'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\EmailVerificationController::class, 'verify'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('attendance.index');

Route::post('/attendance/clock-in', [App\Http\Controllers\AttendanceController::class, 'clockIn'])
    ->middleware(['auth', 'verified'])
    ->name('attendance.clock-in');

Route::post('/attendance/clock-out', [App\Http\Controllers\AttendanceController::class, 'clockOut'])
    ->middleware(['auth', 'verified'])
    ->name('attendance.clock-out');

Route::post('/attendance/break-start', [App\Http\Controllers\AttendanceController::class, 'breakStart'])
    ->middleware(['auth', 'verified'])
    ->name('attendance.break-start');

Route::post('/attendance/break-end', [App\Http\Controllers\AttendanceController::class, 'breakEnd'])
    ->middleware(['auth', 'verified'])
    ->name('attendance.break-end');

Route::get('/attendance/list', [App\Http\Controllers\AttendanceListController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('attendance.list');

Route::get('/attendance/{id}', [App\Http\Controllers\AttendanceDetailController::class, 'show'])
    ->middleware('admin_or_user')
    ->name('attendance.show');

Route::post('/attendance/{id}/correction', [App\Http\Controllers\AttendanceDetailController::class, 'correction'])
    ->middleware('admin_or_user')
    ->name('attendance.correction');

// 申請一覧画面
Route::get('/stamp_correction_request/list', [App\Http\Controllers\StampCorrectionRequestListController::class, 'index'])
    ->middleware(['admin_or_user'])
    ->name('stamp_correction_request.list');

// 申請承認画面（管理者のみ）
Route::get('/stamp_correction_request/approve/{attendance_correct_request}', [App\Http\Controllers\Admin\StampCorrectionRequestApproveController::class, 'show'])
    ->middleware('admin')
    ->name('stamp_correction_request.approve');
Route::post('/stamp_correction_request/approve/{attendance_correct_request}', [App\Http\Controllers\Admin\StampCorrectionRequestApproveController::class, 'approve'])
    ->middleware('admin')
    ->name('stamp_correction_request.approve.post');



// 管理者
Route::prefix('admin')->name('admin.')->group(function () {
    // ログイン関連は認証なしでアクセス可能
    Route::get('/login', [App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login');
    Route::post('/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');

    // その他の管理者機能は認証が必要
    Route::middleware('admin')->group(function () {

    Route::get('/staff/list', [App\Http\Controllers\Admin\StaffListController::class, 'index'])->name('staff.list');

    Route::get('/attendance/staff/{id}', [App\Http\Controllers\Admin\StaffAttendanceController::class, 'show'])->name('attendance.staff');
    Route::get('/attendance/staff/{id}/csv', [App\Http\Controllers\Admin\StaffAttendanceController::class, 'exportCsv'])->name('attendance.staff.csv');

    Route::get('/attendance/list', [App\Http\Controllers\Admin\AttendanceListController::class, 'index'])->name('attendance.list');


    });
});
