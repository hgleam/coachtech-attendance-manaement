<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AttendanceRecord;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * 修正申請承認テスト
 */
class AdminStampCorrectionRequestApproveTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private AttendanceRecord $attendanceRecord;

    /**
     * セットアップ
     */
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用ユーザーを作成
        $this->user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);

        // 承認待ちの修正申請を作成
        $this->attendanceRecord = AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2025-09-15',
            'clock_in_time' => '09:05:00', // 修正申請後の時間
            'clock_out_time' => '18:00:00',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'PENDING',
            'applied_at' => now(),
            'remark' => '電車の遅延により5分遅刻しました。',
        ]);
    }

    /**
     * 15.勤怠情報修正機能（管理者）
     * 修正申請の承認処理が正しく行われる
     */
    public function test_修正申請の承認処理が正しく行われる()
    {
        // 管理者セッションを設定
        session(['admin_id' => 1]);

        // 承認処理を実行
        $response = $this->post("/stamp_correction_request/approve/{$this->attendanceRecord->id}", [
            'action' => 'approve'
        ]);

        $response->assertRedirect('/stamp_correction_request/list');

        $this->attendanceRecord->refresh();

        $this->assertEquals('APPROVED', $this->attendanceRecord->approval_status);

        $this->assertEquals('09:05:00', $this->attendanceRecord->clock_in_time);
    }

    // 以降は実装する上で必要と考えた追加テスト

    /**
     * 15.勤怠情報修正機能（管理者）
     * 管理者が修正申請詳細画面にアクセスできる
     */
    public function test_管理者が修正申請詳細画面にアクセスできる()
    {
        // 管理者セッションを設定
        session(['admin_id' => 1]);

        $response = $this->get("/stamp_correction_request/approve/{$this->attendanceRecord->id}");

        $response->assertStatus(200);
        $response->assertSee('勤怠詳細');
    }

    /**
     * 15.勤怠情報修正機能（管理者）
     * 修正申請の詳細内容が正しく表示されている
     */
    public function test_修正申請の詳細内容が正しく表示されている()
    {
        // 管理者セッションを設定
        session(['admin_id' => 1]);

        $response = $this->get("/stamp_correction_request/approve/{$this->attendanceRecord->id}");

        $response->assertStatus(200);

        // 申請内容が正しく表示されていることを確認
        $response->assertSee('テストユーザー'); // ユーザー名
        $response->assertSee('2025年　　9月15日'); // 申請日
        $response->assertSee('09:05'); // 修正申請後の出勤時間
        $response->assertSee('電車の遅延により5分遅刻しました。'); // 修正理由
    }

    /**
     * 15.勤怠情報修正機能（管理者）
     * 管理者の申請一覧で承認待ちから承認済みに変更されている
     */
    public function test_管理者の申請一覧で承認待ちから承認済みに変更されている()
    {
        // 管理者セッションを設定
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => 'password!!'
        ]);
        session(['admin_id' => $admin->id]);

        // 承認処理を実行
        $approveResponse = $this->post("/stamp_correction_request/approve/{$this->attendanceRecord->id}", [
            'action' => 'approve'
        ]);

        // 承認処理のレスポンスを確認
        if ($approveResponse->status() !== 302) {
            $this->fail("承認処理が失敗しました。ステータス: " . $approveResponse->status() . ", レスポンス: " . $approveResponse->getContent());
        }

        // 勤怠記録の状態を確認
        $this->attendanceRecord->refresh();
        if ($this->attendanceRecord->approval_status !== 'APPROVED') {
            $this->fail("勤怠記録の承認状態が正しくありません。現在の状態: " . $this->attendanceRecord->approval_status);
        }

        // 管理者の申請一覧画面で承認済みタブを確認
        $response = $this->get('/stamp_correction_request/list?status=approved');

        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('承認済み');
    }

    /**
     * 15.勤怠情報修正機能（管理者）
     * 一般ユーザーの修正申請一覧で承認待ちから承認済みに変更されている
     */
    public function test_一般ユーザーの修正申請一覧で承認待ちから承認済みに変更されている()
    {
        // 管理者セッションを設定
        session(['admin_id' => 1]);

        // 承認処理を実行
        $this->post("/admin/stamp_correction_request/approve/{$this->attendanceRecord->id}", [
            'action' => 'approve'
        ]);

        // 一般ユーザーでログインして申請一覧を確認
        $response = $this->actingAs($this->user)->get('/stamp_correction_request/list?status=approved');

        $response->assertStatus(200);
        $response->assertSee('承認済み');
    }

    /**
     * 15.勤怠情報修正機能（管理者）
     * 未認証ユーザーは修正申請詳細画面にアクセスできない
     */
    public function test_未認証ユーザーは修正申請詳細画面にアクセスできない()
    {
        $response = $this->get("/stamp_correction_request/approve/{$this->attendanceRecord->id}");

        $response->assertRedirect('/admin/login');
    }

    /**
     * 15.勤怠情報修正機能（管理者）
     * 一般ユーザーは修正申請詳細画面にアクセスできない
     */
    public function test_一般ユーザーは修正申請詳細画面にアクセスできない()
    {
        $response = $this->actingAs($this->user)->get("/stamp_correction_request/approve/{$this->attendanceRecord->id}");

        $response->assertRedirect('/admin/login');
    }

}
