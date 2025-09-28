<?php

namespace Database\Factories;

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * 勤怠記録ファクトリ
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceRecord>
 */
class AttendanceRecordFactory extends Factory
{
    /**
     * ファクトリの対応するモデルの名前
     *
     * @var string
     */
    protected $model = AttendanceRecord::class;

    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $clockInTime = $this->faker->time('H:i', '10:00');
        $clockOutTime = $this->faker->time('H:i', '19:00');

        return [
            'user_id' => User::factory(),
            'date' => $this->faker->date('Y-m-d'),
            'clock_in_time' => $clockInTime,
            'clock_out_time' => $clockOutTime,
            'work_state' => $this->faker->randomElement(['BEFORE_WORK', 'WORKING', 'ON_BREAK', 'AFTER_LEAVE']),
            'approval_status' => null,
            'applied_at' => null,
            'remark' => null,
            'total_work_time' => null,
            'break_total_time' => null,
        ];
    }
}
