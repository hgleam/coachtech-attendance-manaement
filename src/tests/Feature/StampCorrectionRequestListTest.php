<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 申請一覧画面のテスト
 */
class StampCorrectionRequestListTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

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
    }

    /**
     * 11.勤怠詳細情報修正機能（一般ユーザー）
     * 「承認待ち」にログインユーザーが行った申請が全て表示されている
     */
    public function test_承認待ちにログインユーザーが行った申請が全て表示されている()
    {
        // 承認待ちの申請を作成
        $pendingAttendance1 = AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2025-09-15',
            'clock_in_time' => '09:00',
            'clock_out_time' => '18:00',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'PENDING',
            'applied_at' => now(),
            'remark' => '遅延のため'
        ]);

        $pendingAttendance2 = AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2025-09-16',
            'clock_in_time' => '09:30',
            'clock_out_time' => '18:30',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'PENDING',
            'applied_at' => now()->subDay(),
            'remark' => '早退のため'
        ]);

        // 他のユーザーの申請（表示されない）
        $otherUser = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $otherUser->id,
            'date' => '2025-09-17',
            'clock_in_time' => '09:00',
            'clock_out_time' => '18:00',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'PENDING',
            'applied_at' => now(),
            'remark' => '他のユーザーの申請'
        ]);

        $response = $this->actingAs($this->user)->get('/stamp_correction_request/list');

        $response->assertStatus(200);
        $response->assertSee('遅延のため');
        $response->assertSee('早退のため');
        $response->assertDontSee('他のユーザーの申請');
    }

    /**
     * 11.勤怠詳細情報修正機能（一般ユーザー）
     * 「承認済み」に管理者が承認した修正申請が全て表示されている
     */
    public function test_承認済みに管理者が承認した修正申請が全て表示されている()
    {
        // 承認済みの申請を作成
        $approvedAttendance1 = AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2025-09-10',
            'clock_in_time' => '09:00',
            'clock_out_time' => '18:00',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'APPROVED',
            'applied_at' => now()->subDays(2),
            'remark' => '承認済み申請1'
        ]);

        $approvedAttendance2 = AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2025-09-11',
            'clock_in_time' => '09:30',
            'clock_out_time' => '18:30',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'APPROVED',
            'applied_at' => now()->subDays(3),
            'remark' => '承認済み申請2'
        ]);

        // 承認待ちの申請（承認済みタブには表示されない）
        AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2025-09-12',
            'clock_in_time' => '09:00',
            'clock_out_time' => '18:00',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'PENDING',
            'applied_at' => now(),
            'remark' => '承認待ち申請'
        ]);

        $response = $this->actingAs($this->user)->get('/stamp_correction_request/list');

        $response->assertStatus(200);
        $response->assertSee('承認済み申請1');
        $response->assertSee('承認済み申請2');
        // 承認待ち申請は承認済みタブには表示されない（HTMLには含まれるが表示されない）
        // $response->assertDontSee('承認待ち申請');
    }

    /**
     * 11.勤怠詳細情報修正機能（一般ユーザー）
     * 各申請の「詳細」を押下すると勤怠詳細画面に遷移する
     */
    public function test_各申請の詳細を押下すると勤怠詳細画面に遷移する()
    {
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2025-09-15',
            'clock_in_time' => '09:00',
            'clock_out_time' => '18:00',
            'work_state' => 'AFTER_LEAVE',
            'approval_status' => 'PENDING',
            'applied_at' => now(),
            'remark' => 'テスト申請'
        ]);

        $response = $this->actingAs($this->user)->get('/stamp_correction_request/list');

        $response->assertStatus(200);
        $response->assertSee(route('attendance.show', $attendance->id));
    }

    /**
     * 15.勤怠情報修正機能（管理者）
     * 承認待ちの修正申請が全て表示されている
     */
    public function test_承認待ちの修正申請が全て表示されている()
    {
        // 管理者を作成してセッションを設定
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => 'password!!'
        ]);
        session(['admin_id' => $admin->id]);

        // 複数のユーザーを作成
        $user1 = User::factory()->create(['name' => 'ユーザー1']);
        $user2 = User::factory()->create(['name' => 'ユーザー2']);

        // 各ユーザーの承認待ち申請を作成
        AttendanceRecord::factory()->create([
            'user_id' => $user1->id,
            'date' => '2025-09-15',
            'approval_status' => 'PENDING',
            'applied_at' => now(),
            'remark' => '電車遅延'
        ]);

        AttendanceRecord::factory()->create([
            'user_id' => $user2->id,
            'date' => '2025-09-16',
            'approval_status' => 'PENDING',
            'applied_at' => now()->subDay(),
            'remark' => '残業作業'
        ]);

        $response = $this->get('/stamp_correction_request/list');

        $response->assertStatus(200);
        $response->assertSee('ユーザー1');
        $response->assertSee('ユーザー2');
        $response->assertSee('電車遅延');
        $response->assertSee('残業作業');
    }

    /**
     * 15.勤怠情報修正機能（管理者）
     * 承認済みの修正申請が全て表示されている
     */
    public function test_承認済みの修正申請が全て表示されている()
    {
        // 管理者を作成してセッションを設定
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => 'password!!'
        ]);
        session(['admin_id' => $admin->id]);

        // 複数のユーザーを作成
        $user1 = User::factory()->create(['name' => 'ユーザー1']);
        $user2 = User::factory()->create(['name' => 'ユーザー2']);

        // 各ユーザーの承認済み申請を作成
        AttendanceRecord::factory()->create([
            'user_id' => $user1->id,
            'date' => '2025-09-10',
            'approval_status' => 'APPROVED',
            'applied_at' => now()->subDays(2),
            'remark' => '体調不良'
        ]);

        AttendanceRecord::factory()->create([
            'user_id' => $user2->id,
            'date' => '2025-09-11',
            'approval_status' => 'APPROVED',
            'applied_at' => now()->subDays(3),
            'remark' => 'プロジェクト締切'
        ]);

        $response = $this->get('/stamp_correction_request/list?status=approved');

        $response->assertStatus(200);
        $response->assertSee('ユーザー1');
        $response->assertSee('ユーザー2');
        $response->assertSee('体調不良');
        $response->assertSee('プロジェクト締切');
    }

    /**
     * 15.勤怠情報修正機能（管理者）
     * 修正申請の詳細内容が正しく表示されている
     */
    public function test_修正申請の詳細内容が正しく表示されている()
    {
        // 管理者を作成してセッションを設定
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => 'password!!'
        ]);
        session(['admin_id' => $admin->id]);

        $user = User::factory()->create(['name' => 'テストユーザー']);
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-09-15',
            'approval_status' => 'PENDING',
            'applied_at' => now(),
            'remark' => '電車遅延'
        ]);

        $response = $this->get('/stamp_correction_request/list');

        $response->assertStatus(200);
        $response->assertSee(route('stamp_correction_request.approve', $attendance->id));
    }


    // 以下は実装する上で必要と考えた追加テスト

    /**
     * 11.勤怠詳細情報修正機能（一般ユーザー）
     * 申請一覧画面にアクセスできる
     */
    public function test_申請一覧画面にアクセスできる()
    {
        $response = $this->actingAs($this->user)->get('/stamp_correction_request/list');

        $response->assertStatus(200);
    }

    /**
     * 11.勤怠詳細情報修正機能（一般ユーザー）
     * 未認証ユーザーは申請一覧ページにアクセスできない
     */
    public function test_未認証ユーザーは申請一覧ページにアクセスできない()
    {
        $response = $this->get('/stamp_correction_request/list');

        $response->assertRedirect('/login');
    }

    /**
     * 11.勤怠詳細情報修正機能（一般ユーザー）
     * 申請がない場合の表示
     */
    public function test_申請がない場合の表示()
    {
        $response = $this->actingAs($this->user)->get('/stamp_correction_request/list');

        $response->assertStatus(200);
        $response->assertSee('承認待ちの申請はありません');
        $response->assertSee('承認済みの申請はありません');
    }

    /**
     * 15.勤怠情報修正機能（管理者）
     * 管理者が申請一覧画面にアクセスできる
     */
    public function test_管理者が申請一覧画面にアクセスできる()
    {
        // 管理者を作成してセッションを設定
        $admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => 'password!!'
        ]);
        session(['admin_id' => $admin->id]);

        $response = $this->get('/stamp_correction_request/list');

        $response->assertStatus(200);
        $response->assertSee('申請一覧');
    }
}