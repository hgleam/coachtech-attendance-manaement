<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 管理者ログインのテスト
 */
class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    /**
     * テスト環境をセットアップ
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'email' => 'admin@example.com',
            'password' => 'password!!'
        ]);
    }

    /**
     * 3.ログイン認証機能（管理者）
     * メールアドレスが未入力の場合、バリデーションメッセージが表示される
     * @return void
     */
    public function test_メールアドレスが未入力の場合バリデーションメッセージが表示される()
    {
        $response = $this->post('/admin/login', [
            'email' => '',
            'password' => 'password!!'
        ]);

        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * 3.ログイン認証機能（管理者）
     * パスワードが未入力の場合、バリデーションメッセージが表示される
     * @return void
     */
    public function test_パスワードが未入力の場合バリデーションメッセージが表示される()
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => ''
        ]);

        $response->assertSessionHasErrors(['password']);
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /**
     * 3.ログイン認証機能（管理者）
     * 登録内容と一致しない場合、バリデーションメッセージが表示される
     * @return void
     */
    public function test_登録内容と一致しない場合バリデーションメッセージが表示される()
    {
        $response = $this->post('/admin/login', [
            'email' => 'wrong@example.com',
            'password' => 'password!!'
        ]);

        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }

    // 以下は実装する上で必要と考えた追加テスト

    /**
     * 3.ログイン認証機能（管理者）
     * 管理者ログイン画面にアクセスできる
     * @return void
     */
    public function test_管理者ログイン画面にアクセスできる()
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('管理者ログイン');
    }

    /**
     * 3.ログイン認証機能（管理者）
     * 正しい認証情報でログインできる
     * @return void
     */
    public function test_正しい認証情報でログインできる()
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password!!'
        ]);

        $response->assertRedirect('/admin/attendance/list');
        $this->assertEquals($this->admin->id, session('admin_id'));
    }

    /**
     * 3.ログイン認証機能（管理者）
     * 間違ったパスワードでログインできない
     * @return void
     */
    public function test_間違ったパスワードでログインできない()
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }

    /**
     * 3.ログイン認証機能（管理者）
     * 管理者ログアウトできる
     * @return void
     */
    public function test_管理者ログアウトできる()
    {
        // まずログイン
        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password!!'
        ]);

        // ログアウト
        $response = $this->post('/admin/logout');

        $response->assertRedirect('/admin/login');
        $this->assertNull(session('admin_id'));
    }
}