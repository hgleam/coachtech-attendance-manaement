<?php

namespace Tests\Feature;

use App\Constants\Attendance;
use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 勤怠機能のテスト
 */
class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 6.出勤機能
     * 出勤ボタンが正しく機能する
     */
    public function test_勤務外のユーザーが出勤できる()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/attendance/clock-in');

        $response->assertRedirect('/attendance');
        $response->assertSessionHas('status', '出勤しました');

        $this->assertDatabaseHas('attendance_records', [
            'user_id' => $user->id,
            'date' => today(),
            'work_state' => 'WORKING',
        ]);
    }

    /**
     * 6.出勤機能
     * 出勤は一日一回のみできる
     */
    public function test_出勤は一日一回のみできる()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 最初の出勤
        $this->actingAs($user)->post('/attendance/clock-in');

        // 2回目の出勤を試行
        $response = $this->actingAs($user)->post('/attendance/clock-in');

        $response->assertSessionHasErrors(['attendance' => '本日は既に出勤済みです']);

        // データベースに1件のみ記録されていることを確認
        $this->assertEquals(1, AttendanceRecord::where('user_id', $user->id)
            ->where('date', today())
            ->count());
    }

    /**
     * 7.休憩機能
     * 休憩ボタンが正しく機能する
     */
    public function test_休憩ボタンが正しく機能する()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 出勤
        $this->actingAs($user)->post('/attendance/clock-in');

        // 休憩入
        $response = $this->actingAs($user)->post('/attendance/break-start');

        $response->assertRedirect('/attendance');
        $response->assertSessionHas('status', '休憩を開始しました');

        $this->assertDatabaseHas('attendance_records', [
            'user_id' => $user->id,
            'date' => today(),
            'work_state' => 'ON_BREAK',
        ]);
    }

    /**
     * 7.休憩機能
     * 休憩は一日に何回でもできる
     */
    public function test_休憩は一日に何回でもできる()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 出勤
        $this->actingAs($user)->post('/attendance/clock-in');

        // 1回目の休憩
        $this->actingAs($user)->post('/attendance/break-start');
        $this->actingAs($user)->post('/attendance/break-end');

        // 2回目の休憩
        $this->actingAs($user)->post('/attendance/break-start');
        $this->actingAs($user)->post('/attendance/break-end');

        // 3回目の休憩開始
        $response = $this->actingAs($user)->post('/attendance/break-start');

        $response->assertRedirect('/attendance');
        $response->assertSessionHas('status', '休憩を開始しました');

        // 画面上に「休憩入」ボタンが表示されることを確認
        $viewResponse = $this->actingAs($user)->get('/attendance');
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('休憩戻');
        $viewResponse->assertSee('休憩中');

        // データベースに3つの休憩記録が作成されていることを確認
        $this->assertEquals(3, BreakRecord::whereHas('attendanceRecord', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where('date', today());
        })->count());
    }

    /**
     * 7.休憩機能
     * 休憩戻ボタンが正しく機能する
     */
    public function test_休憩戻ボタンが正しく機能する()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 出勤して休憩入
        /** @var \App\Models\User $user */
        $this->actingAs($user)->post('/attendance/clock-in');
        $this->actingAs($user)->post('/attendance/break-start');

        // 休憩戻
        $response = $this->actingAs($user)->post('/attendance/break-end');

        $response->assertRedirect('/attendance');
        $response->assertSessionHas('status', '休憩を終了しました');

        $this->assertDatabaseHas('attendance_records', [
            'user_id' => $user->id,
            'date' => today(),
            'work_state' => 'WORKING',
        ]);
    }

    /**
     * 7.休憩機能
     * 休憩戻は一日に何回でもできる
     */
    public function test_休憩戻は一日に何回でもできる()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 出勤
        $this->actingAs($user)->post('/attendance/clock-in');

        // 1回目の休憩（開始→終了）
        $this->actingAs($user)->post('/attendance/break-start');
        $this->actingAs($user)->post('/attendance/break-end');

        // 2回目の休憩（開始→終了）
        $this->actingAs($user)->post('/attendance/break-start');
        $this->actingAs($user)->post('/attendance/break-end');

        // 3回目の休憩開始
        $this->actingAs($user)->post('/attendance/break-start');

        // 3回目の休憩戻
        $response = $this->actingAs($user)->post('/attendance/break-end');

        $response->assertRedirect('/attendance');
        $response->assertSessionHas('status', '休憩を終了しました');

        // 画面上に「休憩戻」ボタンが表示されることを確認
        $viewResponse = $this->actingAs($user)->get('/attendance');
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('休憩入');
        $viewResponse->assertSee('退勤');
        $viewResponse->assertSee('出勤中');

        // データベースに3つの休憩記録が作成されていることを確認
        $this->assertEquals(3, BreakRecord::whereHas('attendanceRecord', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where('date', today());
        })->count());
    }

    /**
     * 8.退勤機能
     * 退勤ボタンが正しく機能する
     */
    public function test_退勤ボタンが正しく機能する()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 出勤
        /** @var \App\Models\User $user */
        $this->actingAs($user)->post('/attendance/clock-in');

        // 退勤前の画面表示確認
        $beforeResponse = $this->actingAs($user)->get('/attendance');
        $beforeResponse->assertStatus(200);
        $beforeResponse->assertSee('退勤');
        $beforeResponse->assertSee('出勤中');

        // 退勤
        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->post('/attendance/clock-out');

        $response->assertRedirect('/attendance');
        $response->assertSessionHas('status', 'お疲れ様でした。');

        $this->assertDatabaseHas('attendance_records', [
            'user_id' => $user->id,
            'work_state' => 'AFTER_LEAVE',
        ]);

        // 退勤時間が設定されていることを確認
        $attendance = \App\Models\AttendanceRecord::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();
        $this->assertNotNull($attendance);
        $this->assertNotNull($attendance->clock_out_time);

        // 退勤後の画面表示確認
        $afterResponse = $this->actingAs($user)->get('/attendance');
        $afterResponse->assertStatus(200);
        $afterResponse->assertSee('退勤済');
        $afterResponse->assertDontSee('type="submit"'); // ボタンが表示されないことを確認
        $afterResponse->assertDontSee('出勤中');
    }

    // 以下は実装する上で必要と考えた追加テスト

    /**
     * 6.出勤機能
     * 退勤済のユーザーは出勤ボタンが表示されない
     */
    public function test_退勤済のユーザーは出勤できない()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 出勤して退勤
        $this->actingAs($user)->post('/attendance/clock-in');
        $this->actingAs($user)->post('/attendance/clock-out');

        // 再度出勤を試行
        $response = $this->actingAs($user)->post('/attendance/clock-in');

        $response->assertSessionHasErrors(['attendance' => '本日は既に退勤済みです']);
    }

    /**
     * 7.休憩機能
     * 勤務外のユーザーは休憩入できない
     */
    public function test_勤務外のユーザーは休憩入できない()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/attendance/break-start');

        $response->assertSessionHasErrors(['attendance' => '出勤していないため休憩できません']);
    }

    /**
     * 7.休憩機能
     * 休憩中でないユーザーは休憩戻できない
     */
    public function test_休憩中でないユーザーは休憩戻できない()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->post('/attendance/break-end');

        $response->assertSessionHasErrors(['attendance' => '出勤していないため休憩終了できません']);
    }

    /**
     * 8.退勤機能
     * 出勤していないユーザーは退勤できない
     */
    public function test_出勤していないユーザーは退勤できない()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->post('/attendance/clock-out');

        $response->assertSessionHasErrors(['attendance' => '出勤していないため退勤できません']);
    }

    /**
     * 8.退勤機能
     * 退勤は1日に1回だけできる
     */
    public function test_退勤は一日一回のみできる()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 出勤して退勤
        /** @var \App\Models\User $user */
        $this->actingAs($user)->post('/attendance/clock-in');
        $this->actingAs($user)->post('/attendance/clock-out');

        // 再度退勤を試行
        $response = $this->actingAs($user)->post('/attendance/clock-out');

        $response->assertSessionHasErrors(['attendance' => '本日は既に退勤済みです']);
    }

    /**
     * 6.出勤機能
     * 勤務外のユーザーには出勤ボタンが表示される
     */
    public function test_勤務外のユーザーには出勤ボタンが表示される()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('出勤');
        $response->assertSee('勤務外');
    }

    /**
     * 6.出勤機能
     * 出勤中のユーザーには休憩入と退勤ボタンが表示される
     */
    public function test_出勤中のユーザーには休憩入と退勤ボタンが表示される()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 出勤
        $this->actingAs($user)->post('/attendance/clock-in');

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('休憩入');
        $response->assertSee('退勤');
        $response->assertSee('出勤中');
    }

    /**
     * 7.休憩機能
     * 休憩中のユーザーには休憩戻ボタンが表示される
     */
    public function test_休憩中のユーザーには休憩戻ボタンが表示される()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 出勤して休憩入
        $this->actingAs($user)->post('/attendance/clock-in');
        $this->actingAs($user)->post('/attendance/break-start');

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('休憩戻');
        $response->assertSee('休憩中');
    }

    /**
     * 8.退勤機能
     * 退勤済のユーザーにはボタンが表示されない
     */
    public function test_退勤済のユーザーにはボタンが表示されない()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 出勤して退勤
        $this->actingAs($user)->post('/attendance/clock-in');
        $this->actingAs($user)->post('/attendance/clock-out');

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('退勤済');
        $response->assertDontSee('出勤');
        $response->assertDontSee('休憩入');
        $response->assertDontSee('type="submit"');
    }
}
