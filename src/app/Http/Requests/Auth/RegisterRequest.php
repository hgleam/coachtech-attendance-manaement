<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

/**
 * 登録リクエスト
 */
class RegisterRequest extends FormRequest
{
    /**
     * リクエストを許可するかどうかを判定
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * リクエストのバリデーションルールを取得
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'min:8'],
            'password_confirmation' => ['required', 'same:password'],
        ];
    }


    /**
     * バリデーションエラーのカスタム属性を取得
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => '名前',
            'email' => 'メールアドレス',
            'password' => 'パスワード',
            'password_confirmation' => 'パスワード確認',
        ];
    }
}
