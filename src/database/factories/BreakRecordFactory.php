<?php

namespace Database\Factories;

use App\Models\AttendanceRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * 休憩記録ファクトリ
 */
class BreakRecordFactory extends Factory
{
    /**
     * モデルのデフォルト状態を定義
     *
     * @return array
     */
    public function definition()
    {
        $startTime = $this->faker->time('H:i', '15:00');
        $endTime = $this->faker->time('H:i', '16:00');

        return [
            'attendance_record_id' => AttendanceRecord::factory(),
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
    }
}
