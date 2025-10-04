<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 勤怠詳細画面のテスト
 */
class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private AttendanceRecord $attendance;

    /**
     * セットアップ
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $this->attendance = AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2025-09-09',
            'clock_in_time' => '09:00',
            'clock_out_time' => '18:00',
            'work_state' => 'AFTER_LEAVE'
        ]);

        // 休憩記録を作成
        BreakRecord::factory()->create([
            'attendance_record_id' => $this->attendance->id,
            'start_time' => '12:00',
            'end_time' => '13:00'
        ]);
    }

    /**
     * 11.勤怠詳細情報修正機能（一般ユーザー）
     * 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
     * @return void
     */
    public function test_出勤時間が退勤時間より後の場合エラーメッセージが表示される()
    {
        $response = $this->actingAs($this->user)->post("/attendance/{$this->attendance->id}/correction", [
            'clock_in_time' => '19:00:00',
            'clock_out_time' => '18:00:00',
            'break_start_time' => ['12:00'],
            'break_end_time' => ['13:00'],
            'remark' => 'テスト備考'
        ]);

        $response->assertSessionHasErrors(['clock_in_time']);
        $response->assertSessionHasErrors(['clock_out_time']);
    }

    /**
     * 11.勤怠詳細情報修正機能（一般ユーザー）
     * 休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
     * @return void
     */
    public function test_休憩開始時間が退勤時間より後の場合エラーメッセージが表示される()
    {
        $response = $this->actingAs($this->user)->post("/attendance/{$this->attendance->id}/correction", [
            'clock_in_time' => '09:00:00',
            'clock_out_time' => '18:00:00',
            'break_start_time' => ['19:00'],
            'break_end_time' => ['20:00'],
            'remark' => 'テスト備考'
        ]);

        $response->assertSessionHasErrors(['break_start_time.0']);
    }

    /**
     * 11.勤怠詳細情報修正機能（一般ユーザー）
     * 休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
     * @return void
     */
    public function test_休憩終了時間が退勤時間より後の場合エラーメッセージが表示される()
    {
        $response = $this->actingAs($this->user)->post("/attendance/{$this->attendance->id}/correction", [
            'clock_in_time' => '09:00:00',
            'clock_out_time' => '18:00:00',
            'break_start_time' => ['12:00'],
            'break_end_time' => ['19:00'],
            'remark' => 'テスト備考'
        ]);

        $response->assertSessionHasErrors(['break_end_time.0']);
    }

    /**
     * 11.勤怠詳細情報修正機能（一般ユーザー）
     * 備考欄が未入力の場合のエラーメッセージが表示される
     * @return void
     */
    public function test_備考欄が未入力の場合エラーメッセージが表示される()
    {
        $response = $this->actingAs($this->user)->post("/attendance/{$this->attendance->id}/correction", [
            'clock_in_time' => '09:00:00',
            'clock_out_time' => '18:00:00',
            'break_start_time' => ['12:00'],
            'break_end_time' => ['13:00'],
            'remark' => ''
        ]);

        $response->assertSessionHasErrors(['remark']);
    }

    /**
     * 11.勤怠詳細情報修正機能（一般ユーザー）
     * 修正申請処理が実行される
     * @return void
     */
    public function test_修正申請処理が実行される()
    {
        $response = $this->actingAs($this->user)->post("/attendance/{$this->attendance->id}/correction", [
            'clock_in_time' => '09:30:00',
            'clock_out_time' => '18:30:00',
            'break_start_time' => ['12:00'],
            'break_end_time' => ['13:00'],
            'remark' => '修正申請のテスト'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', '修正申請を送信しました。');

        // 勤怠記録が更新され、承認待ち状態になっていることを確認
        $this->attendance->refresh();
        $this->assertEquals('09:30:00', $this->attendance->clock_in_time);
        $this->assertEquals('18:30:00', $this->attendance->clock_out_time);
        $this->assertEquals('修正申請のテスト', $this->attendance->remark);
        $this->assertEquals('PENDING', $this->attendance->approval_status);
        $this->assertNotNull($this->attendance->applied_at);

        // 休憩記録が更新されていることを確認
        $breakRecords = $this->attendance->breakRecords;
        $this->assertCount(1, $breakRecords);
        $this->assertEquals('12:00', $breakRecords->first()->start_time->format('H:i'));
        $this->assertEquals('13:00', $breakRecords->first()->end_time->format('H:i'));
    }

        /**
     * 13.勤怠詳細情報取得・修正機能（管理者）
     * 勤怠詳細画面に表示されるデータが選択したものになっている
     * @return void
     */
    public function test_管理者勤怠詳細画面に表示されるデータが選択したものになっている()
    {
        // 管理者を作成してセッションを設定
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => 'password!!'
        ]);
        session(['admin_id' => $admin->id]);

        $response = $this->get("/attendance/{$this->attendance->id}");

        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        $response->assertSee('2025年　　　　　　9月9日');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }

    /**
     * 13.勤怠詳細情報取得・修正機能（管理者）
     * 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
     * @return void
     */
    public function test_管理者出勤時間が退勤時間より後の場合エラーメッセージが表示される()
    {
        // 管理者を作成してセッションを設定
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => 'password!!'
        ]);
        session(['admin_id' => $admin->id]);

        $response = $this->post("/attendance/{$this->attendance->id}/correction", [
            'clock_in_time' => '19:00:00',
            'clock_out_time' => '18:00:00',
            'break_start_time' => ['12:00'],
            'break_end_time' => ['13:00'],
            'remark' => 'テスト備考'
        ]);

        $response->assertSessionHasErrors(['clock_in_time']);
        $response->assertSessionHasErrors(['clock_out_time']);
    }

    /**
     * 13.勤怠詳細情報取得・修正機能（管理者）
     * 休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
     * @return void
     */
    public function test_管理者休憩開始時間が退勤時間より後の場合エラーメッセージが表示される()
    {
        // 管理者を作成してセッションを設定
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => 'password!!'
        ]);
        session(['admin_id' => $admin->id]);

        $response = $this->post("/attendance/{$this->attendance->id}/correction", [
            'clock_in_time' => '09:00:00',
            'clock_out_time' => '18:00:00',
            'break_start_time' => ['19:00'],
            'break_end_time' => ['20:00'],
            'remark' => 'テスト備考'
        ]);

        $response->assertSessionHasErrors(['break_start_time.0']);
    }

    /**
     * 13.勤怠詳細情報取得・修正機能（管理者）
     * 休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
     * @return void
     */
    public function test_管理者休憩終了時間が退勤時間より後の場合エラーメッセージが表示される()
    {
        // 管理者を作成してセッションを設定
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => 'password!!'
        ]);
        session(['admin_id' => $admin->id]);

        $response = $this->post("/attendance/{$this->attendance->id}/correction", [
            'clock_in_time' => '09:00:00',
            'clock_out_time' => '18:00:00',
            'break_start_time' => ['12:00'],
            'break_end_time' => ['19:00'],
            'remark' => 'テスト備考'
        ]);

        $response->assertSessionHasErrors(['break_end_time.0']);
    }

    /**
     * 13.勤怠詳細情報取得・修正機能（管理者）
     * 備考欄が未入力の場合のエラーメッセージが表示される
     * @return void
     */
    public function test_管理者備考欄が未入力の場合エラーメッセージが表示される()
    {
        // 管理者を作成してセッションを設定
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => 'password!!'
        ]);
        session(['admin_id' => $admin->id]);

        $response = $this->post("/attendance/{$this->attendance->id}/correction", [
            'clock_in_time' => '09:00:00',
            'clock_out_time' => '18:00:00',
            'break_start_time' => ['12:00'],
            'break_end_time' => ['13:00'],
            'remark' => ''
        ]);

        $response->assertSessionHasErrors(['remark']);
    }

    // 以下は実装する上で必要と考えた追加テスト

    /**
     * 11.勤怠詳細情報修正機能（一般ユーザー）
     * 勤怠詳細画面にアクセスできる
     * @return void
     */
    public function test_勤怠詳細画面にアクセスできる()
    {
        $response = $this->actingAs($this->user)->get("/attendance/{$this->attendance->id}");

        $response->assertStatus(200);
    }

    /**
     * 11.勤怠詳細情報修正機能（一般ユーザー）
     * 勤怠詳細画面の「名前」がログインユーザーの氏名になっている
     * @return void
     */
    public function test_勤怠詳細画面の名前がログインユーザーの氏名になっている()
    {
        $response = $this->actingAs($this->user)->get("/attendance/{$this->attendance->id}");

        $response->assertStatus(200);
        $response->assertSee($this->user->name);
    }

    /**
     * 11.勤怠詳細情報修正機能（一般ユーザー）
     * 勤怠詳細画面の「日付」が選択した日付になっている
     * @return void
     */
    public function test_勤怠詳細画面の日付が選択した日付になっている()
    {
        $response = $this->actingAs($this->user)->get("/attendance/{$this->attendance->id}");

        $response->assertStatus(200);
        $response->assertSee('2025年　　　　　　9月9日');
    }

    /**
     * 11.勤怠詳細情報修正機能（一般ユーザー）
     * 「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致している
     * @return void
     */
    public function test_出勤退勤時間がログインユーザーの打刻と一致している()
    {
        $response = $this->actingAs($this->user)->get("/attendance/{$this->attendance->id}");

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /**
     * 11.勤怠詳細情報修正機能（一般ユーザー）
     * 「休憩」にて記されている時間がログインユーザーの打刻と一致している
     * @return void
     */
    public function test_休憩時間がログインユーザーの打刻と一致している()
    {
        $response = $this->actingAs($this->user)->get("/attendance/{$this->attendance->id}");

        $response->assertStatus(200);
        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }

    /**
     * 11.勤怠詳細情報修正機能（一般ユーザー）
     * 休憩回数分のレコードと追加で１つ分の入力フィールドが表示される
     * @return void
     */
    public function test_休憩回数分のレコードと追加の入力フィールドが表示される()
    {
        // 2つ目の休憩記録を追加
        BreakRecord::factory()->create([
            'attendance_record_id' => $this->attendance->id,
            'start_time' => '15:00',
            'end_time' => '15:15'
        ]);

        $response = $this->actingAs($this->user)->get("/attendance/{$this->attendance->id}");

        $response->assertStatus(200);
        // 既存の2つの休憩記録 + 1つの追加入力フィールド = 3つの休憩入力欄が表示される
        $response->assertSee('break_start_time[]');
        $response->assertSee('break_end_time[]');
    }

    /**
     * 11.勤怠詳細情修正機能（一般ユーザー）
     * 未認証ユーザーは勤怠詳細ページにアクセスできない
     * @return void
     */
    public function test_未認証ユーザーは勤怠詳細ページにアクセスできない()
    {
        $response = $this->get("/attendance/{$this->attendance->id}");

        $response->assertRedirect('/login');
    }

    /**
     * 11.勤怠詳細情修正機能（一般ユーザー）
     * 他のユーザーの勤怠詳細ページにアクセスできない
     * @return void
     */
    public function test_他のユーザーの勤怠詳細ページにアクセスできない()
    {
        $otherUser = User::factory()->create();
        $otherAttendance = AttendanceRecord::factory()->create([
            'user_id' => $otherUser->id,
            'date' => '2025-09-09',
            'clock_in_time' => '09:00',
            'clock_out_time' => '18:00',
            'work_state' => 'AFTER_LEAVE'
        ]);

        $response = $this->actingAs($this->user)->get("/attendance/{$otherAttendance->id}");

        $response->assertStatus(403);
    }

    /**
     * 11.勤怠詳細情修正機能（一般ユーザー）
     * 退勤完了前の勤怠記録は修正できない
     * @return void
     */
    public function test_申請済みの勤怠記録は修正できない()
    {
        // 申請済みの勤怠記録を作成
        $approvedAttendance = AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2025-09-10',
            'clock_in_time' => '09:00',
            'clock_out_time' => '18:00',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'APPROVED'
        ]);

        $response = $this->actingAs($this->user)->post("/attendance/{$approvedAttendance->id}/correction", [
            'clock_in_time' => '09:30:00',
            'clock_out_time' => '18:30:00',
            'break_start_time' => ['12:00'],
            'break_end_time' => ['13:00'],
            'remark' => '修正のテスト'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'この勤怠記録は既に承認済みです。修正申請はできません。');
    }

    /**
     * 11.勤怠詳細情修正機能（一般ユーザー）
     * 申請中の勤怠記録は修正申請できない
     * @return void
     */
    public function test_申請中の勤怠記録は修正申請できない()
    {
        // 申請中の勤怠記録を作成
        $pendingAttendance = AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2025-09-11',
            'clock_in_time' => '09:00',
            'clock_out_time' => '18:00',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'PENDING'
        ]);

        $response = $this->actingAs($this->user)->post("/attendance/{$pendingAttendance->id}/correction", [
            'clock_in_time' => '09:30:00',
            'clock_out_time' => '18:30:00',
            'break_start_time' => ['12:00'],
            'break_end_time' => ['13:00'],
            'remark' => '修正のテスト'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'この勤怠記録は既に修正申請中です。承認されるまで新しい修正申請はできません。');
    }

    /**
     * 11.勤怠詳細情修正機能（一般ユーザー）
     * 備考欄に既存の値がデフォルト表示される
     * @return void
     */
    public function test_備考欄に既存の値がデフォルト表示される()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'APPROVED',
            'remark' => '既存の備考内容'
        ]);

        $this->actingAs($user);

        $response = $this->get(route('attendance.show', $attendance->id));

        $response->assertStatus(200);
        $response->assertSee('既存の備考内容');
    }

    /**
     * 11.勤怠詳細情修正機能（一般ユーザー）
     * 管理者でログイン中は管理画面用のヘッダーが表示される
     * @return void
     */
    public function test_管理者でログイン中は管理画面用のヘッダーが表示される()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'APPROVED'
        ]);

        // 管理者を作成してセッションを設定
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => 'password!!'
        ]);
        session(['admin_id' => $admin->id]);

        $response = $this->get(route('attendance.show', $attendance->id));

        $response->assertStatus(200);
        // 管理者用ナビゲーションが表示される
        $response->assertSee('スタッフ一覧');
        $response->assertSee('勤怠一覧');
        $response->assertSee('申請一覧');
        // 申請一覧は管理者用なので、一般ユーザー用の「申請」リンクは表示されない
        $response->assertDontSee('href="http://localhost/stamp_correction_request/list"');
    }

    /**
     * 11.勤怠詳細情修正機能（一般ユーザー）
     * 申請中の勤怠記録は修正できない
     * @return void
     */
    public function test_申請中の勤怠記録は修正できない()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'PENDING'
        ]);

        $this->actingAs($user);

        $response = $this->get(route('attendance.show', $attendance->id));

        $response->assertStatus(200);
        // 修正ボタンは表示されない
        $response->assertDontSee('type="submit"');
        // 申請中のメッセージが表示される
        $response->assertSee('承認待ちのため修正はできません。');
    }

    /**
     * 11.勤怠詳細情修正機能（一般ユーザー）
     * 一般ユーザーでログイン中は一般ユーザー用のヘッダーが表示される
     * @return void
     */
    public function test_一般ユーザーでログイン中は一般ユーザー用のヘッダーが表示される()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'APPROVED'
        ]);

        $this->actingAs($user);

        $response = $this->get(route('attendance.show', $attendance->id));

        $response->assertStatus(200);
        // 一般ユーザー用ナビゲーションが表示される
        $response->assertSee('勤怠');
        $response->assertSee('勤怠一覧');
        $response->assertSee('申請');
        // 管理者用ナビゲーションは表示されない
        $response->assertDontSee('スタッフ一覧');
        $response->assertDontSee('修正申請一覧');
    }

    /**
     * 11.勤怠詳細情修正機能（一般ユーザー）
     * 承認済みの勤怠は修正不可で承認済みボタンが表示される
     * @return void
     */
    public function test_承認済みの勤怠は修正不可で承認済みボタンが表示される()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'APPROVED',
            'applied_at' => now()
        ]);

        // 管理者を作成してセッションを設定
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => 'password!!'
        ]);
        session(['admin_id' => $admin->id]);

        // デバッグ: 作成された勤怠記録の値を確認
        $attendance->refresh();
        $this->assertEquals('APPROVED', $attendance->approval_status);

        $response = $this->get(route('attendance.show', $attendance->id));

        $response->assertStatus(200);
        // 修正ボタンは表示されない（type="submit"のボタンは表示されない）
        $response->assertDontSee('type="submit"');
        // 承認済みボタンが表示される
        $response->assertSee('承認済み');
    }
}
