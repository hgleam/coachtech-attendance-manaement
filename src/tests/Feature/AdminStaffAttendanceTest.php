<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * 管理者用スタッフ月次勤怠画面のテスト
 */
class AdminStaffAttendanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * セットアップ
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 管理者ユーザーを作成
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => 'password!!',
        ]);
    }

    /**
     * 14.ユーザー情報取得機能（管理者）
     * ユーザーの勤怠情報が正しく表示される
     */
    public function test_ユーザーの勤怠情報が正しく表示される()
    {
        // 一般ユーザーを作成
        $user = User::create([
            'name' => '西 伶奈',
            'email' => 'reina.n@coachtech.com',
            'password' => bcrypt('password'),
        ]);

        // 複数の勤怠記録を作成
        $attendance1 = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => '2025-09-01',
            'clock_in_time' => '09:00:00',
            'clock_out_time' => '18:00:00',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'APPROVED',
            'remark' => '通常勤務',
        ]);

        $attendance2 = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => '2025-09-02',
            'clock_in_time' => '09:30:00',
            'clock_out_time' => '18:30:00',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'APPROVED',
            'remark' => '遅刻',
        ]);

        // 管理者でログイン
        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password!!',
        ]);

        // 9月の勤怠画面にアクセス
        $response = $this->get("/admin/attendance/staff/{$user->id}?month=2025-09");

        // ユーザー名が表示される
        $response->assertSee('西 伶奈さんの勤怠');

        // 勤怠情報が正確に表示される
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('09:30');
        $response->assertSee('18:30');

        // 詳細リンクが表示される
        $response->assertSee('/attendance/1');
        $response->assertSee('/attendance/2');
    }

    /**
     * 14.ユーザー情報取得機能（管理者）
     * 「前月」を押下した時に表示月の前月の情報が表示される
     */
    public function test_前月を押下した時に表示月の前月の情報が表示される()
    {
        // 一般ユーザーを作成
        $user = User::create([
            'name' => '西 伶奈',
            'email' => 'reina.n@coachtech.com',
            'password' => bcrypt('password'),
        ]);

        // 8月の勤怠記録を作成
        $attendance = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => '2025-08-15',
            'clock_in_time' => '09:00:00',
            'clock_out_time' => '18:00:00',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'APPROVED',
        ]);

        // 管理者でログイン
        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password!!',
        ]);

        // 9月の勤怠画面にアクセス
        $response = $this->get("/admin/attendance/staff/{$user->id}?month=2025-09");

        // 前月（8月）へのリンクが表示される
        $response->assertSee('/admin/attendance/staff/' . $user->id . '?month=2025-08');

        // 前月のリンクをクリック
        $response = $this->get("/admin/attendance/staff/{$user->id}?month=2025-08");

        // 前月の情報が表示される
        $response->assertSee('2025/08');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /**
     * 14.ユーザー情報取得機能（管理者）
     * 「翌月」を押下した時に表示月の翌月の情報が表示される
     */
    public function test_翌月を押下した時に表示月の翌月の情報が表示される()
    {
        // 一般ユーザーを作成
        $user = User::create([
            'name' => '西 伶奈',
            'email' => 'reina.n@coachtech.com',
            'password' => bcrypt('password'),
        ]);

        // 10月の勤怠記録を作成
        $attendance = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => '2025-10-15',
            'clock_in_time' => '09:00:00',
            'clock_out_time' => '18:00:00',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'APPROVED',
        ]);

        // 管理者でログイン
        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password!!',
        ]);

        // 9月の勤怠画面にアクセス
        $response = $this->get("/admin/attendance/staff/{$user->id}?month=2025-09");

        // 翌月（10月）へのリンクが表示される
        $response->assertSee('/admin/attendance/staff/' . $user->id . '?month=2025-10');

        // 翌月のリンクをクリック
        $response = $this->get("/admin/attendance/staff/{$user->id}?month=2025-10");

        // 翌月の情報が表示される
        $response->assertSee('2025/10');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /**
     * 14.ユーザー情報取得機能（管理者）
     * 「詳細」を押下すると、その日の勤怠詳細画面に遷移する
     */
    public function test_詳細を押下するとその日の勤怠詳細画面に遷移する()
    {
        // 一般ユーザーを作成
        $user = User::create([
            'name' => '西 伶奈',
            'email' => 'reina.n@coachtech.com',
            'password' => bcrypt('password'),
        ]);

        // 勤怠記録を作成
        $attendance = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => '2025-09-15',
            'clock_in_time' => '09:00:00',
            'clock_out_time' => '18:00:00',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'APPROVED',
        ]);

        // 管理者でログイン
        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password!!',
        ]);

        // 9月の勤怠画面にアクセス
        $response = $this->get("/admin/attendance/staff/{$user->id}?month=2025-09");

        // 詳細リンクが表示される
        $response->assertSee('/attendance/' . $attendance->id);

        // 詳細リンクをクリック
        $response = $this->get("/admin/attendance/{$attendance->id}");

        // 勤怠詳細画面に遷移する
        $response->assertStatus(200);
        $response->assertSee('西 伶奈');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    // 以降は実装する上で必要と考えた追加テスト

    /**
     * 14.ユーザー情報取得機能（管理者）
     * 管理者がスタッフの月次勤怠画面にアクセスできる
     */
    public function test_管理者がスタッフの月次勤怠画面にアクセスできる()
    {
        // 一般ユーザーを作成
        $user = User::create([
            'name' => '西 伶奈',
            'email' => 'reina.n@coachtech.com',
            'password' => bcrypt('password'),
        ]);

        // 管理者でログイン
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password!!',
        ]);

        // スタッフの月次勤怠画面にアクセス
        $response = $this->get("/admin/attendance/staff/{$user->id}");
        $response->assertStatus(200);
        $response->assertViewIs('admin.attendances.staff');
    }

    /**
     * 14.ユーザー情報取得機能（管理者）
     * 現在の月の勤怠データが表示される
     */
    public function test_現在の月の勤怠データが表示される()
    {
        // 一般ユーザーを作成
        $user = User::create([
            'name' => '西 伶奈',
            'email' => 'reina.n@coachtech.com',
            'password' => bcrypt('password'),
        ]);

        // 現在の月の勤怠記録を作成
        $attendance = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'clock_in_time' => '09:00:00',
            'clock_out_time' => '18:00:00',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'APPROVED',
        ]);

        // 作成した勤怠記録を確認
        $this->assertDatabaseHas('attendance_records', [
            'user_id' => $user->id,
            'date' => now()->format('Y-m-d') . ' 00:00:00',
        ]);

        // 管理者でログイン
        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password!!',
        ]);

        // スタッフの月次勤怠画面にアクセス
        $response = $this->get("/admin/attendance/staff/{$user->id}");

        // 現在の月が表示される
        $response->assertSee(now()->format('Y/m'));

        // ユーザー名が表示される
        $response->assertSee('西 伶奈さんの勤怠');

        // 勤怠データが表示される
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /**
     * 14.ユーザー情報取得機能（管理者）
     * 指定された年月の勤怠データが表示される
     */
    public function test_指定された年月の勤怠データが表示される()
    {
        // 一般ユーザーを作成
        $user = User::create([
            'name' => '西 伶奈',
            'email' => 'reina.n@coachtech.com',
            'password' => bcrypt('password'),
        ]);

        // 8月の勤怠記録を作成
        $attendance = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => '2025-08-15',
            'clock_in_time' => '09:30:00',
            'clock_out_time' => '18:30:00',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'APPROVED',
        ]);

        // 管理者でログイン
        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password!!',
        ]);

        // 8月の勤怠画面にアクセス
        $response = $this->get("/admin/attendance/staff/{$user->id}?month=2025-08");

        // 8月が表示される
        $response->assertSee('2025/08');

        // 8月の勤怠データが表示される
        $response->assertSee('09:30');
        $response->assertSee('18:30');
    }

    /**
     * 14.ユーザー情報取得機能（管理者）
     * 休憩時間と総労働時間が正しく計算される
     */
    public function test_休憩時間と総労働時間が正しく計算される()
    {
        // 一般ユーザーを作成
        $user = User::create([
            'name' => '西 伶奈',
            'email' => 'reina.n@coachtech.com',
            'password' => bcrypt('password'),
        ]);

        // 勤怠記録を作成
        $attendance = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'clock_in_time' => '09:00:00',
            'clock_out_time' => '18:00:00',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'APPROVED',
        ]);

        // 休憩記録を作成
        BreakRecord::create([
            'attendance_record_id' => $attendance->id,
            'start_time' => '12:00:00',
            'end_time' => '13:00:00',
        ]);

        // 管理者でログイン
        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password!!',
        ]);

        // スタッフの月次勤怠画面にアクセス
        $response = $this->get("/admin/attendance/staff/{$user->id}");

        // 休憩時間が表示される
        $response->assertSee('1:00');

        // 総労働時間が表示される
        $response->assertSee('8:00');
    }

    /**
     * 14.ユーザー情報取得機能（管理者）
     * 未認証の管理者はスタッフの月次勤怠画面にアクセスできない
     */
    public function test_未認証の管理者はスタッフの月次勤怠画面にアクセスできない()
    {
        // 一般ユーザーを作成
        $user = User::create([
            'name' => '西 伶奈',
            'email' => 'reina.n@coachtech.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->get("/admin/attendance/staff/{$user->id}");
        $response->assertRedirect('/admin/login');
    }

    /**
     * 14.ユーザー情報取得機能（管理者）
     * 管理者がスタッフの月次勤怠データをCSV出力できる
     */
    public function test_管理者がスタッフの月次勤怠データをCSV出力できる()
    {
        // 一般ユーザーを作成
        $user = User::create([
            'name' => '西 伶奈',
            'email' => 'reina.n@coachtech.com',
            'password' => bcrypt('password'),
        ]);

        // 現在の月の勤怠記録を作成
        $attendance = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'clock_in_time' => '09:00:00',
            'clock_out_time' => '18:00:00',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'APPROVED',
            'remark' => 'テストデータ',
        ]);

        // 管理者でログイン
        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password!!',
        ]);

        // CSV出力をリクエスト
        $response = $this->get("/admin/attendance/staff/{$user->id}/csv");

        // CSVレスポンスが返される
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // Content-Dispositionヘッダーを確認
        $contentDisposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('attachment; filename=', $contentDisposition);

        // CSV内容を確認
        $csvContent = $response->getContent();
        $this->assertStringContainsString('日付,曜日,出勤時間,退勤時間,休憩時間,総労働時間,備考', $csvContent);
        $this->assertStringContainsString('09:00', $csvContent);
        $this->assertStringContainsString('18:00', $csvContent);
        $this->assertStringContainsString('テストデータ', $csvContent);
    }

    /**
     * 14.ユーザー情報取得機能（管理者）
     * 指定された年月の勤怠データをCSV出力できる
     */
    public function test_指定された年月の勤怠データをCSV出力できる()
    {
        // 一般ユーザーを作成
        $user = User::create([
            'name' => '西 伶奈',
            'email' => 'reina.n@coachtech.com',
            'password' => bcrypt('password'),
        ]);

        // 8月の勤怠記録を作成
        $attendance = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => '2025-08-15',
            'clock_in_time' => '09:30:00',
            'clock_out_time' => '18:30:00',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'APPROVED',
            'remark' => '8月データ',
        ]);

        // 管理者でログイン
        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password!!',
        ]);

        // 8月のCSV出力をリクエスト
        $response = $this->get("/admin/attendance/staff/{$user->id}/csv?month=2025-08");

        // CSVレスポンスが返される
        $response->assertStatus(200);

        // Content-Dispositionヘッダーを確認
        $contentDisposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('attachment; filename=', $contentDisposition);

        // CSV内容を確認
        $csvContent = $response->getContent();
        $this->assertStringContainsString('2025-08-15', $csvContent);
        $this->assertStringContainsString('09:30', $csvContent);
        $this->assertStringContainsString('18:30', $csvContent);
        $this->assertStringContainsString('8月データ', $csvContent);
    }

    /**
     * 14.ユーザー情報取得機能（管理者）
     * 未認証の管理者はCSV出力できない
     */
    public function test_未認証の管理者はCSV出力できない()
    {
        // 一般ユーザーを作成
        $user = User::create([
            'name' => '西 伶奈',
            'email' => 'reina.n@coachtech.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->get("/admin/attendance/staff/{$user->id}/csv");
        $response->assertRedirect('/admin/login');
    }
}
