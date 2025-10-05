<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

/**
 * 勤怠一覧リクエスト
 */
class AttendanceListRequest extends FormRequest
{
    /**
     * リクエストを許可するかどうかを判定
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'month' => ['nullable', 'date_format:Y-m', function ($attribute, $value, $fail) {
                if ($value && !$this->isValidMonth($value)) {
                    $fail('無効な月です。');
                }
            }],
        ];
    }

    /**
     * バリデーションメッセージ
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'month.date_format' => '月はY-m形式で入力してください。',
        ];
    }

    /**
     * 属性名のカスタマイズ
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'month' => '月',
        ];
    }

    /**
     * 有効な月かどうかをチェック
     *
     * @param string $month
     * @return bool
     */
    private function isValidMonth(string $month): bool
    {
        // 空文字列チェック
        if (empty($month)) {
            return false;
        }

        // Y-m形式の正規表現チェック
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            return false;
        }

        // Carbonでパース可能かチェック（厳密な検証）
        try {
            $parsed = Carbon::createFromFormat('Y-m', $month);
            // パースされた月が元の文字列と一致するかチェック（無効な月を検出）
            return $parsed->format('Y-m') === $month;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 月を取得（デフォルト値付き）
     *
     * @return string
     */
    public function getMonth(): string
    {
        $month = $this->get('month');
        return $month ?: now()->format('Y-m');
    }
}
