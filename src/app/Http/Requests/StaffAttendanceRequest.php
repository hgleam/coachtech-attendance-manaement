<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * スタッフ勤怠リクエスト
 */
class StaffAttendanceRequest extends FormRequest
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
            'year' => 'nullable|integer|min:2000|max:2100',
            'month' => 'nullable|integer|min:1|max:12',
        ];
    }

    /**
     * バリデーションメッセージを取得
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'year.integer' => '年は整数で入力してください。',
            'year.min' => '年は2000年以降で入力してください。',
            'year.max' => '年は2100年以前で入力してください。',
            'month.integer' => '月は整数で入力してください。',
            'month.min' => '月は1月以上で入力してください。',
            'month.max' => '月は12月以下で入力してください。',
        ];
    }

    /**
     * 年月を取得（デフォルト値付き）
     *
     * @return array<string, int>
     */
    public function getYearMonth()
    {
        return [
            'year' => $this->get('year', now()->year),
            'month' => $this->get('month', now()->month)
        ];
    }
}
