<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * 会員登録テスト
 */
class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1.認証機能（一般ユーザー）
     * 名前が未入力の場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_名前が未入力の場合バリデーションメッセージが表示される()
    {
        $userData = [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password!!',
            'password_confirmation' => 'password!!',
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors(['name']);

        $actualMessage = session('errors')->first('name');
        $this->assertNotEmpty($actualMessage);
        $this->assertStringContainsString('名前', $actualMessage);
        $this->assertStringContainsString('入力してください', $actualMessage);
    }

    /**
     * 1.認証機能（一般ユーザー）
     * メールアドレスが未入力の場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_メールアドレスが未入力の場合バリデーションメッセージが表示される()
    {
        $userData = [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password!!',
            'password_confirmation' => 'password!!',
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors(['email']);

        $actualMessage = session('errors')->first('email');
        $this->assertNotEmpty($actualMessage);
        $this->assertStringContainsString('メールアドレス', $actualMessage);
        $this->assertStringContainsString('入力してください', $actualMessage);
    }

    /**
     * 1.認証機能（一般ユーザー）
     * パスワードが8文字未満の場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_パスワードが8文字未満の場合バリデーションメッセージが表示される()
    {
        $userData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '123',
            'password_confirmation' => '123',
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors(['password']);
    }

    /**
     * 1.認証機能（一般ユーザー）
     * パスワードが一致しない場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_パスワードが一致しない場合バリデーションメッセージが表示される()
    {
        $userData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password!!',
            'password_confirmation' => 'different',
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors(['password_confirmation']);

        $actualMessage = session('errors')->first('password_confirmation');
        $this->assertNotEmpty($actualMessage);
        $this->assertStringContainsString('パスワード', $actualMessage);
        $this->assertStringContainsString('一致しません', $actualMessage);
    }

    /**
     * 1.認証機能（一般ユーザー）
     * パスワードが未入力の場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_パスワードが未入力の場合バリデーションメッセージが表示される()
    {
        $userData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors(['password']);

        $actualMessage = session('errors')->first('password');
        $this->assertNotEmpty($actualMessage);
        $this->assertStringContainsString('パスワード', $actualMessage);
        $this->assertStringContainsString('入力してください', $actualMessage);
    }

    /**
     * 1.認証機能（一般ユーザー）
     * フォームに内容が入力されていた場合、データが正常に保存される
     *
     * @return void
     */
    public function test_会員登録が成功する()
    {
        $userData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password!!',
            'password_confirmation' => 'password!!',
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect('/email/verify');

        // データベースにユーザーが保存されていることを確認
        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertTrue(Hash::check('password!!', $user->password));
        $this->assertNull($user->email_verified_at);
    }

    // 以下は実装する上で必要と考えた追加テスト

    /**
     * 1.認証機能（一般ユーザー）
     * 会員登録画面が表示される
     *
     * @return void
     */
    public function test_会員登録画面が表示される()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    /**
     * 1.認証機能（一般ユーザー）
     * メールアドレスが重複している場合エラーになる
     *
     * @return void
     */
    public function test_メールアドレスが重複している場合エラーになる()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $userData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password!!',
            'password_confirmation' => 'password!!',
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors(['email']);

        $actualMessage = session('errors')->first('email');
        $this->assertNotEmpty($actualMessage);
        $this->assertStringContainsString('メールアドレス', $actualMessage);
        $this->assertStringContainsString('既に使用されています', $actualMessage);
    }

    /**
     * 1.認証機能（一般ユーザー）
     * 全項目が未入力の場合、バリデーションエラーが発生する
     *
     * @return void
     */
    public function test_全項目が未入力の場合バリデーションエラーが発生する()
    {
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }
}
