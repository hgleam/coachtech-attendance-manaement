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
    return view('welcome');
});

// 仮ルーティング
// 一般ユーザー
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');

Route::get('/attendance', function () {
    // NOTE: 本来はロジックで出し分けるが、デザイン確認のため全ての状態を一度に表示
    return view('attendances.index');
})->name('attendance.index');

Route::get('/attendance/list', function () {
    return view('attendances.list');
})->name('attendance.list');

Route::get('/attendance/{id}', function ($id) {
    // NOTE: 承認待ちのデザインも確認できるよう、両方のパターンを含める
    return view('attendances.show', ['id' => $id]);
})->name('attendance.show');

Route::get('/stamp_correction_request/list', function () {
    return view('stamp_correction_requests.list');
})->name('stamp_correction_request.list');


// 管理者
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', function () {
        return view('admin.login');
    })->name('login');

    Route::get('/staff/list', function () {
        return view('admin.staff.list');
    })->name('staff.list');

    Route::get('/attendance/staff/{id}', function ($id) {
        return view('admin.attendances.staff', ['id' => $id]);
    })->name('attendance.staff');

    Route::get('/attendance/list', function () {
        return view('admin.attendances.list');
    })->name('attendance.list');

    Route::get('/stamp_correction_request/list', function () {
        return view('admin.stamp_correction_requests.list');
    })->name('stamp_correction_request.list');

    Route::get('/stamp_correction_request/approve/{attendance_correct_request}', function ($id) {
        return view('admin.stamp_correction_requests.approve', ['id' => $id]);
    })->name('stamp_correction_request.approve');
});
