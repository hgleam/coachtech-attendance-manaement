<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 勤怠修正のバリデーション
 */
class AttendanceUpdateRequest extends FormRequest
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
            'clock_in_time' => ['required', function ($attribute, $value, $fail) {
                if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $value)) {
                    $fail('出勤時間の形式が正しくありません');
                }
            }],
            'clock_out_time' => ['required', function ($attribute, $value, $fail) {
                if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $value)) {
                    $fail('退勤時間の形式が正しくありません');
                }
            }],
            'break_start_time' => ['array'],
            'break_start_time.*' => ['nullable', 'date_format:H:i'],
            'break_end_time' => ['array'],
            'break_end_time.*' => ['nullable', 'date_format:H:i'],
            'remark' => ['required', 'string', 'max:1000']
        ];
    }

    /**
     * カスタムバリデーションルール
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateClockTimes($validator);
            $this->validateBreakTimes($validator);
        });
    }

    /**
     * 出勤・退勤時間のバリデーション
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    private function validateClockTimes($validator)
    {
        $clockInTime = $this->input('clock_in_time');
        $clockOutTime = $this->input('clock_out_time');

        if (!$clockInTime || !$clockOutTime) {
            return;
        }

        $clockInTimeFormatted = $this->formatTimeForComparison($clockInTime);
        $clockOutTimeFormatted = $this->formatTimeForComparison($clockOutTime);

        if ($clockInTimeFormatted >= $clockOutTimeFormatted) {
            $validator->errors()->add('clock_in_time', '出勤時間もしくは退勤時間が不適切な値です');
            $validator->errors()->add('clock_out_time', '出勤時間もしくは退勤時間が不適切な値です');
        }
    }

    /**
     * 休憩時間のバリデーション
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    private function validateBreakTimes($validator)
    {
        $breakStartTimes = $this->input('break_start_time', []);
        $breakEndTimes = $this->input('break_end_time', []);
        $clockInTime = $this->input('clock_in_time');
        $clockOutTime = $this->input('clock_out_time');

        foreach ($breakStartTimes as $index => $breakStartTime) {
            $breakEndTime = $breakEndTimes[$index] ?? null;

            if ($this->shouldSkipBreakValidation($breakStartTime, $breakEndTime)) {
                continue;
            }

            if ($breakStartTime && $breakEndTime) {
                $this->validateSingleBreakTime($validator, $index, $breakStartTime, $breakEndTime, $clockInTime, $clockOutTime);
            }
        }
    }

    /**
     * 休憩時間のバリデーションをスキップするかどうかを判定
     *
     * @param string|null $breakStartTime
     * @param string|null $breakEndTime
     * @return bool
     */
    private function shouldSkipBreakValidation($breakStartTime, $breakEndTime)
    {
        return empty($breakStartTime) && empty($breakEndTime);
    }

    /**
     * 単一の休憩時間のバリデーション
     *
     * @param \Illuminate\Validation\Validator $validator
     * @param int $index
     * @param string $breakStartTime
     * @param string $breakEndTime
     * @param string $clockInTime
     * @param string $clockOutTime
     * @return void
     */
    private function validateSingleBreakTime($validator, $index, $breakStartTime, $breakEndTime, $clockInTime, $clockOutTime)
    {
        $clockInTimeFormatted = $this->formatTimeForComparison($clockInTime);
        $clockOutTimeFormatted = $this->formatTimeForComparison($clockOutTime);
        $breakStartTimeFormatted = $this->formatTimeForComparison($breakStartTime);
        $breakEndTimeFormatted = $this->formatTimeForComparison($breakEndTime);

        // 休憩開始時間が出勤時間より前の場合
        if ($breakStartTimeFormatted < $clockInTimeFormatted) {
            $validator->errors()->add("break_start_time.{$index}", '休憩時間が不適切な値です');
        }

        // 休憩開始時間が退勤時間より後の場合
        if ($breakStartTimeFormatted >= $clockOutTimeFormatted) {
            $validator->errors()->add("break_start_time.{$index}", '休憩時間が不適切な値です');
        }

        // 休憩終了時間が退勤時間より後の場合
        if ($breakEndTimeFormatted > $clockOutTimeFormatted) {
            $validator->errors()->add("break_end_time.{$index}", '休憩時間もしくは退勤時間が不適切な値です');
        }

        // 休憩終了時間が休憩開始時間より前の場合（同じ時間は許可）
        if ($breakEndTimeFormatted < $breakStartTimeFormatted) {
            $validator->errors()->add("break_end_time.{$index}", '休憩時間が不適切な値です');
        }
    }

    /**
     * 時間を統一形式（H:i:s）に変換
     */
    private function formatTimeForComparison($time)
    {
        if (empty($time)) {
            return null;
        }

        // H:i形式の場合は秒を追加
        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
            return $time . ':00';
        }

        // H:i:s形式の場合はそのまま
        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
            return $time;
        }

        return $time;
    }

    /**
     * バリデーションメッセージ
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'clock_in_time.required' => '出勤時間を入力してください',
            'clock_in_time.date_format' => '出勤時間の形式が不正です',
            'clock_out_time.required' => '退勤時間を入力してください',
            'clock_out_time.date_format' => '退勤時間の形式が不正です',
            'break_start_time.*.date_format' => '休憩開始時間の形式が不正です',
            'break_end_time.*.date_format' => '休憩終了時間の形式が不正です',
            'remark.required' => '備考を記入してください',
            'remark.max' => '備考は1000文字以内で入力してください'
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
            'clock_in_time' => '出勤時間',
            'clock_out_time' => '退勤時間',
            'break_start_time.*' => '休憩開始時間',
            'break_end_time.*' => '休憩終了時間',
            'remark' => '備考'
        ];
    }
}