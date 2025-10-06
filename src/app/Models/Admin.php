<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * 管理者モデル
 */
class Admin extends Authenticatable
{
    use HasFactory;

    /**
     * フィルタリング可能な属性
     * @var array<string>
     */
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * 非表示属性
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * キャスト属性
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * パスワードが正しいかチェック
     * @param string $password
     * @return bool
     */
    public function checkPassword(string $password): bool
    {
        return \Illuminate\Support\Facades\Hash::check($password, $this->getAttribute('password'));
    }

    /**
     * メールアドレスとパスワードで認証
     * @param string $email
     * @param string $password
     * @return Admin|null
     */
    public static function authenticate(string $email, string $password): ?self
    {
        /** @var \App\Models\Admin|null $admin */
        $admin = static::query()->where('email', $email)->first();

        if (!$admin || !$admin->checkPassword($password)) {
            return null;
        }

        return $admin;
    }

    /**
     * パスワードをハッシュ化して保存
     * @param string $value
     * @return void
     */
    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = \Illuminate\Support\Facades\Hash::make($value);
    }
}
