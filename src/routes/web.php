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

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');

Route::get('/attendance', function () {
    // NOTE: 本来はロジックで出し分けるが、デザイン確認のため全ての状態を一度に表示
    return view('attendances.index');
})->name('attendance.index');

Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])
    ->middleware('auth')
    ->name('attendance.index');

Route::post('/attendance/clock-in', [App\Http\Controllers\AttendanceController::class, 'clockIn'])
    ->middleware('auth')
    ->name('attendance.clock-in');

Route::get('/stamp_correction_request/list', function () {
    return view('stamp_correction_requests.list');
})->name('stamp_correction_request.list');
Route::post('/attendance/clock-out', [App\Http\Controllers\AttendanceController::class, 'clockOut'])
    ->middleware('auth')
    ->name('attendance.clock-out');

Route::post('/attendance/break-start', [App\Http\Controllers\AttendanceController::class, 'breakStart'])
    ->middleware('auth')
    ->name('attendance.break-start');

Route::post('/attendance/break-end', [App\Http\Controllers\AttendanceController::class, 'breakEnd'])
    ->middleware('auth')
    ->name('attendance.break-end');

Route::get('/attendance/list', [App\Http\Controllers\AttendanceListController::class, 'index'])
    ->middleware('auth')
    ->name('attendance.list');

Route::get('/attendance/{id}', [App\Http\Controllers\AttendanceDetailController::class, 'show'])
    ->middleware('admin_or_user')
    ->name('attendance.show');

Route::post('/attendance/{id}/correction', [App\Http\Controllers\AttendanceDetailController::class, 'correction'])
    ->middleware('admin_or_user')
    ->name('attendance.correction');

// 申請一覧画面
Route::get('/stamp_correction_request/list', [App\Http\Controllers\StampCorrectionRequestListController::class, 'index'])
    ->middleware('admin_or_user')
    ->name('stamp_correction_request.list');


// 管理者
Route::prefix('admin')->name('admin.')->group(function () {
    // ログイン関連は認証なしでアクセス可能
    Route::get('/login', [App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login');
    Route::post('/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');

    Route::get('/staff/list', function () {
        return view('admin.staff.list');
    })->name('staff.list');

    Route::get('/attendance/staff/{id}', function ($id) {
        return view('admin.attendances.staff', ['id' => $id]);
    })->name('attendance.staff');
    Route::get('/staff/list', [App\Http\Controllers\Admin\StaffListController::class, 'index'])->name('staff.list');

    Route::get('/attendance/list', [App\Http\Controllers\Admin\AttendanceListController::class, 'index'])->name('attendance.list');

    Route::get('/stamp_correction_request/list', function () {
        return view('admin.stamp_correction_requests.list');
    })->name('stamp_correction_request.list');

    Route::get('/stamp_correction_request/approve/{attendance_correct_request}', function ($id) {
        return view('admin.stamp_correction_requests.approve', ['id' => $id]);
    })->name('stamp_correction_request.approve');
});
