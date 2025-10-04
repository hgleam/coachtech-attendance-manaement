<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * 管理者用スタッフ一覧画面のテスト
 */
class AdminStaffListTest extends TestCase
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
            'password' => 'password!!', // モデルの$castsで自動的にハッシュ化される
        ]);
    }

    /**
     * 14.ユーザー情報取得機能（管理者）
     * 管理者ユーザーが全一般ユーザーの「氏名」「メールアドレス」を確認できる
     */
    public function test_管理者ユーザーが全一般ユーザーの氏名とメールアドレスを確認できる()
    {
        // 一般ユーザーを作成
        $user1 = User::create([
            'name' => '西 伶奈',
            'email' => 'reina.n@coachtech.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::create([
            'name' => '山田 太郎',
            'email' => 'taro.y@coachtech.com',
            'password' => bcrypt('password'),
        ]);

        $user3 = User::create([
            'name' => '増田 一世',
            'email' => 'issei.m@coachtech.com',
            'password' => bcrypt('password'),
        ]);

        // 管理者でログイン
        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password!!',
        ]);

        // スタッフ一覧画面にアクセス
        $response = $this->get('/admin/staff/list');

        // 全ての一般ユーザーの氏名とメールアドレスが表示されている
        $response->assertSee('西 伶奈');
        $response->assertSee('reina.n@coachtech.com');
        $response->assertSee('山田 太郎');
        $response->assertSee('taro.y@coachtech.com');
        $response->assertSee('増田 一世');
        $response->assertSee('issei.m@coachtech.com');
    }

    // 以降は実装する上で必要と考えた追加テスト

    /**
     * 14.ユーザー情報取得機能（管理者）
     * 管理者スタッフ一覧画面にアクセスできる
     */
    public function test_管理者スタッフ一覧画面にアクセスできる()
    {
        // 管理者でログイン
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password!!',
        ]);

        // スタッフ一覧画面にアクセス
        $response = $this->get('/admin/staff/list');
        $response->assertStatus(200);
        $response->assertViewIs('admin.staff.list');
    }

    /**
     * 14.ユーザー情報取得機能（管理者）
     * 詳細を押下することによって各ユーザーの月次勤怠一覧に遷移すること
     */
    public function test_詳細を押下することによって各ユーザーの月次勤怠一覧に遷移すること()
    {
        // 一般ユーザーを作成
        $user = User::create([
            'name' => '西 伶奈',
            'email' => 'reina.n@coachtech.com',
            'password' => bcrypt('password'),
        ]);

        // 管理者でログイン
        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password!!',
        ]);

        // スタッフ一覧画面にアクセス
        $response = $this->get('/admin/staff/list');

        // 詳細リンクが正しいURLで表示されている
        $response->assertSee('/admin/attendance/staff/' . $user->id);
    }

    /**
     * 14.ユーザー情報取得機能（管理者）
     * 未認証の管理者はスタッフ一覧画面にアクセスできない
     */
    public function test_未認証の管理者はスタッフ一覧画面にアクセスできない()
    {
        $response = $this->get('/admin/staff/list');
        $response->assertRedirect('/admin/login');
    }
}
