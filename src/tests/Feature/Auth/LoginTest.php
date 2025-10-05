<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * ログインテスト
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 2.ログイン機能
     * メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_メールアドレスが未入力の場合バリデーションメッセージが表示される()
    {
        $userData = [
            'email' => '',
            'password' => 'password!!',
        ];

        $response = $this->post('/login', $userData);

        $response->assertSessionHasErrors(['email']);

        $actualMessage = session('errors')->first('email');
        $this->assertNotEmpty($actualMessage);
        $this->assertStringContainsString('メールアドレス', $actualMessage);
        $this->assertStringContainsString('入力してください', $actualMessage);
    }

    /**
     * 2.ログイン機能
     * パスワードが入力されていない場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_パスワードが未入力の場合バリデーションメッセージが表示される()
    {
        $userData = [
            'email' => 'test@example.com',
            'password' => '',
        ];

        $response = $this->post('/login', $userData);

        $response->assertSessionHasErrors(['password']);

        $actualMessage = session('errors')->first('password');
        $this->assertNotEmpty($actualMessage);
        $this->assertStringContainsString('パスワード', $actualMessage);
        $this->assertStringContainsString('入力してください', $actualMessage);
    }

    /**
     * 2.ログイン機能
     * 入力情報が間違っている場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_登録内容と一致しない場合バリデーションメッセージが表示される()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password!!'),
        ]);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->post('/login', $userData);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

     /**
     * 2.ログイン機能
     * 正しい情報が入力された場合、ログイン処理が実行される
     *
     * @return void
     */
    public function test_ログインが成功する()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password!!'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password!!',
        ]);

        $response->assertRedirect('/attendance');
        $this->assertAuthenticated();
    }

    // 以下は実装する上で必要と考えた追加テスト

    /**
     * 2.ログイン機能
     * ログイン画面が表示される
     *
     * @return void
     */
    public function test_ログイン画面が表示される()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /**
     * 2.ログイン機能
     * 存在しないメールアドレスでログインが失敗する
     *
     * @return void
     */
    public function test_存在しないメールアドレスでログインが失敗する()
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password!!',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * 2.ログイン機能
     * 間違ったパスワードでログインが失敗する
     *
     * @return void
     */
    public function test_間違ったパスワードでログインが失敗する()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password!!'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * 2.ログイン機能
     * 全項目が未入力の場合、バリデーションエラーが発生する
     *
     * @return void
     */
    public function test_全項目が未入力の場合バリデーションエラーが発生する()
    {
        $response = $this->post('/login', []);

        $response->assertSessionHasErrors(['email', 'password']);
    }

}
