<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

/**
 * 管理者勤怠一覧のテスト
 */
class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 管理者
     */
    private Admin $admin;

    /**
     * ユーザー1
     */
    private User $user1;

    /**
     * ユーザー2
     */
    private User $user2;

    /**
     * セットアップ
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 管理者を作成
        $this->admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => 'password!!' // モデルの$castsで自動的にハッシュ化される
        ]);

        // ユーザーを作成
        $this->user1 = User::factory()->create([
            'name' => 'ユーザー1',
            'email' => 'user1@example.com'
        ]);

        $this->user2 = User::factory()->create([
            'name' => 'ユーザー2',
            'email' => 'user2@example.com'
        ]);
    }

    /**
     * 12.勤怠一覧情報取得機能（管理者）
     * その日になされた全ユーザーの勤怠情報が正確に確認できる
     */
    public function test_その日になされた全ユーザーの勤怠情報が正確に確認できる()
    {
        // 管理者としてログイン
        session(['admin_id' => $this->admin->id]);

        // 今日の勤怠記録を作成
        $today = now()->format('Y-m-d');

        $attendance1 = AttendanceRecord::factory()->create([
            'user_id' => $this->user1->id,
            'date' => $today,
            'clock_in_time' => '09:00',
            'clock_out_time' => '18:00',
            'work_state' => 'AFTER_LEAVE'
        ]);

        $attendance2 = AttendanceRecord::factory()->create([
            'user_id' => $this->user2->id,
            'date' => $today,
            'clock_in_time' => '09:30',
            'clock_out_time' => '18:30',
            'work_state' => 'AFTER_LEAVE'
        ]);

        $response = $this->get('/admin/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('ユーザー1');
        $response->assertSee('ユーザー2');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('09:30');
        $response->assertSee('18:30');
    }

    /**
     * 12.勤怠一覧情報取得機能（管理者）
     * 遷移した際に現在の日付が表示される
     */
    public function test_遷移した際に現在の日付が表示される()
    {
        // 管理者としてログイン
        session(['admin_id' => $this->admin->id]);

        $response = $this->get('/admin/attendance/list');

        $response->assertStatus(200);
        $response->assertSee(now()->format('Y年n月j日'));
    }

    /**
     * 12.勤怠一覧情報取得機能（管理者）
     * 「前日」を押下した時に前の日の勤怠情報が表示される
     */
    public function test_前日を押下した時に前の日の勤怠情報が表示される()
    {
        // 管理者としてログイン
        session(['admin_id' => $this->admin->id]);

        // 昨日の勤怠記録を作成
        $yesterday = now()->subDay()->format('Y-m-d');

        AttendanceRecord::factory()->create([
            'user_id' => $this->user1->id,
            'date' => $yesterday,
            'clock_in_time' => '08:30',
            'clock_out_time' => '17:30',
            'work_state' => 'AFTER_LEAVE'
        ]);

        $response = $this->get('/admin/attendance/list?date=' . $yesterday);

        $response->assertStatus(200);
        $response->assertSee(Carbon::createFromFormat('Y-m-d', $yesterday)->format('Y年n月j日'));
        $response->assertSee('08:30');
        $response->assertSee('17:30');
    }

    /**
     * 12.勤怠一覧情報取得機能（管理者）
     * 「翌日」を押下した時に次の日の勤怠情報が表示される
     */
    public function test_翌日を押下した時に次の日の勤怠情報が表示される()
    {
        // 管理者としてログイン
        session(['admin_id' => $this->admin->id]);

        // 明日の勤怠記録を作成
        $tomorrow = now()->addDay()->format('Y-m-d');

        AttendanceRecord::factory()->create([
            'user_id' => $this->user1->id,
            'date' => $tomorrow,
            'clock_in_time' => '10:00',
            'clock_out_time' => '19:00',
            'work_state' => 'AFTER_LEAVE'
        ]);

        $response = $this->get('/admin/attendance/list?date=' . $tomorrow);

        $response->assertStatus(200);
        $response->assertSee(Carbon::createFromFormat('Y-m-d', $tomorrow)->format('Y年n月j日'));
        $response->assertSee('10:00');
        $response->assertSee('19:00');
    }

    // 以下は実装する上で必要と考えた追加テスト

    /**
     * 12.勤怠一覧情報取得機能（管理者）
     * 管理者勤怠一覧画面にアクセスできる
     */
    public function test_管理者勤怠一覧画面にアクセスできる()
    {
        // 管理者としてログイン
        session(['admin_id' => $this->admin->id]);

        $response = $this->get('/admin/attendance/list');

        $response->assertStatus(200);
        $response->assertSee(now()->format('Y年n月j日') . 'の勤怠');
    }

    /**
     * 12.勤怠一覧情報取得機能（管理者）
     * 勤怠記録がないユーザーも表示される
     */
    public function test_勤怠記録がないユーザーも表示される()
    {
        // 管理者としてログイン
        session(['admin_id' => $this->admin->id]);

        // 勤怠記録を作成しない
        $response = $this->get('/admin/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('ユーザー1');
        $response->assertSee('ユーザー2');
        // 勤怠記録がない場合は空欄が表示される
        $response->assertSee('<td></td>', false);
    }

    /**
     * 12.勤怠一覧情報取得機能（管理者）
     * 各勤怠の「詳細」を押下すると勤怠詳細画面に遷移する
     */
    public function test_各勤怠の詳細を押下すると勤怠詳細画面に遷移する()
    {
        // 管理者としてログイン
        session(['admin_id' => $this->admin->id]);

        // 勤怠記録を作成
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $this->user1->id,
            'date' => now()->format('Y-m-d'),
            'clock_in_time' => '09:00',
            'clock_out_time' => '18:00',
            'work_state' => 'AFTER_LEAVE'
        ]);

        $response = $this->get('/admin/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('/attendance/' . $attendance->id);
    }
}