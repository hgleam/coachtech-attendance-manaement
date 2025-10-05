<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * 勤怠データサービスクラス
 */
class AttendanceDataService
{
    /**
     * 月次ナビゲーション用の日付データを取得
     * @param string|null $month
     * @return array
     */
    public function getMonthNavigationData($month = null)
    {
        // 月の検証
        if ($month && !$this->isValidMonth($month)) {
            throw new \InvalidArgumentException('無効な月です');
        }

        $currentMonth = $month ? Carbon::createFromFormat('Y-m', $month) : now();

        return [
            'currentMonth' => $currentMonth,
            'prevMonth' => $currentMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $currentMonth->copy()->addMonth()->format('Y-m'),
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
}
