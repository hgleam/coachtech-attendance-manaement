<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 管理者用スタッフ勤怠リクエスト
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'year' => 'nullable|integer',
            'month' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if ($value && !$this->isValidMonth($value)) {
                        $fail('無効な年月です');
                    }
                },
            ],
        ];
    }

    /**
     * 年月を取得（デフォルト値付き）
     *
     * @return array<string, int>
     */
    public function getYearMonth()
    {
        $year = $this->get('year');
        $month = $this->get('month');

        // monthがY-m形式の場合
        if ($month && preg_match('/^\d{4}-\d{2}$/', $month)) {
            $carbon = \Carbon\Carbon::createFromFormat('Y-m', $month);
            return [
                'year' => $carbon->year,
                'month' => $carbon->month
            ];
        }

        return [
            'year' => $year ? (int)$year : now()->year,
            'month' => $month ? (int)$month : now()->month
        ];
    }

    /**
     * 月の妥当性をチェック
     *
     * @param string $month
     * @return bool
     */
    private function isValidMonth(string $month): bool
    {
        // 基本的な形式チェック（Y-m形式）
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            return false;
        }

        try {
            $carbon = \Carbon\Carbon::createFromFormat('Y-m', $month);
            // 元の文字列と一致するかチェック（2025-13のような場合は2026-01になってしまう）
            return $carbon->format('Y-m') === $month;
        } catch (\Exception $e) {
            return false;
        }
    }

}
