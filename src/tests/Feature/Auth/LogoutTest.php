<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ログアウトテスト
 */
class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 3.ログアウト機能
     * ログアウトができる
     *
     * @return void
     */
    public function test_ログアウトが成功する()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
