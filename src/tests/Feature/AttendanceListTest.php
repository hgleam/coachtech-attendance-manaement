<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 勤怠一覧テスト
 */
class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    /**
     * セットアップ
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * 9.勤怠一覧情報取得機能（一般ユーザー）
     * 自分が行った勤怠情報が全て表示されている
     */
    public function test_自分の勤怠情報が全て表示されている()
    {
        // 現在の月の勤怠情報を作成
        $currentMonth = now()->format('Y-m');
        $attendance1 = AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => $currentMonth . '-01',
            'clock_in_time' => '09:00',
            'clock_out_time' => '18:00',
            'work_state' => 'AFTER_LEAVE'
        ]);

        $attendance2 = AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => $currentMonth . '-02',
            'clock_in_time' => '09:30',
            'clock_out_time' => '18:30',
            'work_state' => 'AFTER_LEAVE'
        ]);

        // 他のユーザーの勤怠情報（表示されないはず）
        $otherUser = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $otherUser->id,
            'date' => $currentMonth . '-01',
            'clock_in_time' => '10:00',
            'clock_out_time' => '19:00',
            'work_state' => 'AFTER_LEAVE'
        ]);

        $response = $this->actingAs($this->user)->get('/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('09:30');
        $response->assertSee('18:30');
        // 他のユーザーの情報は表示されない
        $response->assertDontSee('10:00');
        $response->assertDontSee('19:00');
    }

    /**
     * 9.勤怠一覧情報取得機能（一般ユーザー）
     * 勤怠一覧画面に遷移した際に現在の月が表示される
            */
    public function test_勤怠一覧画面に遷移した際に現在の月が表示される()
    {
        $response = $this->actingAs($this->user)->get('/attendance/list');

        $response->assertStatus(200);
        $response->assertSee(date('Y年n月'));
    }

    /**
     * 9.勤怠一覧情報取得機能（一般ユーザー）
     * 「前月」を押下した時に表示月の前月の情報が表示される
     */
    public function test_前月ボタンを押下した時に表示月の前月の情報が表示される()
    {
        // 前月の勤怠情報を作成
        $lastMonth = now()->subMonth();
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => $lastMonth->format('Y-m-01'),
            'clock_in_time' => '09:00',
            'clock_out_time' => '18:00',
            'work_state' => 'AFTER_LEAVE'
        ]);

        $response = $this->actingAs($this->user)->get('/attendance/list?month=' . $lastMonth->format('Y-m'));

        $response->assertStatus(200);
        $response->assertSee($lastMonth->format('Y年n月'));
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /**
     * 9.勤怠一覧情報取得機能（一般ユーザー）
     * 「翌月」を押下した時に表示月の前月の情報が表示される
     */
    public function test_翌月ボタンを押下した時に表示月の翌月の情報が表示される()
    {
        // 翌月の勤怠情報を作成
        $nextMonth = now()->addMonth();
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => $nextMonth->format('Y-m-01'),
            'clock_in_time' => '09:00',
            'clock_out_time' => '18:00',
            'work_state' => 'AFTER_LEAVE'
        ]);

        $response = $this->actingAs($this->user)->get('/attendance/list?month=' . $nextMonth->format('Y-m'));

        $response->assertStatus(200);
        $response->assertSee($nextMonth->format('Y年n月'));
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /**
     * 9.勤怠一覧情報取得機能（一般ユーザー）
     * 「詳細」を押下すると、その日の勤怠詳細画面に遷移する
     */
    public function test_詳細ボタンを押下するとその日の勤怠詳細画面に遷移する()
    {
        $currentMonth = now()->format('Y-m');
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => $currentMonth . '-01',
            'clock_in_time' => '09:00',
            'clock_out_time' => '18:00',
            'work_state' => 'AFTER_LEAVE'
        ]);

        $response = $this->actingAs($this->user)->get('/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('詳細');
        $response->assertSee(route('attendance.show', $attendance->id));
    }

    // 以下は実装する上で必要と考えた追加テスト

    /**
     * 9.勤怠一覧情報取得機能（一般ユーザー）
     * 勤怠情報が登録されたユーザーで勤怠一覧ページにアクセスできる
     */
    public function test_勤怠情報が登録されたユーザーで勤怠一覧ページにアクセスできる()
    {
        // 勤怠情報を作成
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2025-09-01',
            'clock_in_time' => '09:00',
            'clock_out_time' => '18:00',
            'work_state' => 'AFTER_LEAVE'
        ]);

        $response = $this->actingAs($this->user)->get('/attendance/list');

        $response->assertStatus(200);
        $response->assertViewIs('attendances.list');
    }

    /**
     * 9.勤怠一覧情報取得機能（一般ユーザー）
     * 未認証ユーザーは勤怠一覧ページにアクセスできない
     */
    public function test_未認証ユーザーは勤怠一覧ページにアクセスできない()
    {
        $response = $this->get('/attendance/list');

        $response->assertRedirect('/login');
    }
}
