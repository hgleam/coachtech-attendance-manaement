<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

/**
 * 管理者勤怠一覧リクエスト
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
            'date' => ['nullable', function ($attribute, $value, $fail) {
                if ($value && !$this->isValidDate($value)) {
                    $fail('無効な日付です');
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
            // カスタムバリデーションでメッセージを設定しているため、ここでは空
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
            'date' => '日付',
        ];
    }

    /**
     * 有効な日付かどうかをチェック
     *
     * @param string $date
     * @return bool
     */
    private function isValidDate(string $date): bool
    {
        // 空文字列チェック
        if (empty($date)) {
            return false;
        }

        // Y-m-d形式の正規表現チェック
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return false;
        }

        // Carbonでパース可能かチェック（厳密な検証）
        try {
            $parsed = Carbon::createFromFormat('Y-m-d', $date);
            // パースされた日付が元の文字列と一致するかチェック（無効な日付を検出）
            return $parsed->format('Y-m-d') === $date;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 日付を取得（デフォルト値付き）
     *
     * @return string
     */
    public function getDate(): string
    {
        $date = $this->get('date');
        return $date ?: now()->format('Y-m-d');
    }
}
