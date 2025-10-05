@php
use App\Constants\Attendance;
@endphp

@extends('layouts.app')

@section('content')
<div class='attendance-detail-page'>
    <div class='attendance-detail-page__header'>
        <h2 class='attendance-detail-page__title'>勤怠詳細</h2>
    </div>

    <div class='attendance-detail-card'>

        @if(session('error'))
            <div class='alert alert-danger' style='background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 1rem; margin-bottom: 1rem; border-radius: 4px;'>
                {{ session('error') }}
            </div>
        @endif

        @if(session('status'))
            <div class='alert alert-success' style='background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 1rem; margin-bottom: 1rem; border-radius: 4px;'>
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ session()->has('admin_id') ? route('admin.attendance.correction', $attendance->id) : route('attendance.correction', $attendance->id) }}" method="POST" class='attendance-detail-form'>
            @csrf

            <div class='attendance-detail-form__section'>
                <div class='attendance-detail-form__field attendance-detail-form__field--horizontal'>
                    <label class='attendance-detail-form__label'>名前</label>
                    <div class='attendance-detail-form__value'>{{ $attendance->user->name }}</div>
                </div>
            </div>

            <div class='attendance-detail-form__section'>
                <div class='attendance-detail-form__field attendance-detail-form__field--horizontal'>
                    <label class='attendance-detail-form__label'>日付</label>
                    <div class='attendance-detail-form__value'>{{ $attendance->date->format('Y年　　　　　　n月j日') }}</div>
                </div>
            </div>

            <div class='attendance-detail-form__section'>
                <div class='attendance-detail-form__field attendance-detail-form__field--horizontal'>
                    <label class='attendance-detail-form__label'>出勤・退勤</label>
                    @if($attendance->isApproved())
                        <div class='attendance-detail-form__value'>{{ \Carbon\Carbon::parse($attendance->clock_in_time)->format('H:i') }}　　~　　　　{{ \Carbon\Carbon::parse($attendance->clock_out_time)->format('H:i') }}</div>
                    @else
                        <div class='attendance-detail-form__time-display'>
                            <input type='time'
                                   name='clock_in_time'
                                   value='{{ old("clock_in_time", $attendance->clock_in_time) }}'
                                   class='attendance-detail-form__input @error("clock_in_time") attendance-detail-form__input--error @enderror'
                                   @if(!$attendance->canEdit()) disabled @endif>
                            <span class='attendance-detail-form__time-separator'>~</span>
                            <input type='time'
                                   name='clock_out_time'
                                   value='{{ old("clock_out_time", $attendance->clock_out_time) }}'
                                   class='attendance-detail-form__input @error("clock_out_time") attendance-detail-form__input--error @enderror'
                                   @if(!$attendance->canEdit()) disabled @endif>
                        </div>
                    @endif
                </div>
                @if(!$attendance->isApproved())
                    @error('clock_in_time')
                        <div class='attendance-detail-form__error attendance-detail-form__error--block'>{{ $message }}</div>
                    @enderror
                    @error('clock_out_time')
                        <div class='attendance-detail-form__error attendance-detail-form__error--block'>{{ $message }}</div>
                    @enderror
                @endif
            </div>

            @php
                $breakRecords = $attendance->breakRecords;
                $breakStartTimes = old('break_start_time', $breakRecords->pluck('start_time')->map(function($time) {
                    return $time ? $time->format('H:i') : '';
                })->toArray());
                $breakEndTimes = old('break_end_time', $breakRecords->pluck('end_time')->map(function($time) {
                    return $time ? $time->format('H:i') : '';
                })->toArray());

                // 追加入力フィールド用に1つ追加（old()で取得した値に既に空の要素が含まれていない場合のみ）
                $lastStartTime = end($breakStartTimes);
                $lastEndTime = end($breakEndTimes);

                // 最後の要素が空でない場合、または配列が空の場合のみ追加
                if (empty($breakStartTimes) || (!empty($lastStartTime) && !empty($lastEndTime))) {
                    $breakStartTimes[] = '';
                    $breakEndTimes[] = '';
                }
            @endphp

            @if($attendance->isApproved())
                @foreach($attendance->breakRecords as $index => $breakRecord)
                <div class='attendance-detail-form__section'>
                    <div class='attendance-detail-form__field attendance-detail-form__field--horizontal'>
                        <label class='attendance-detail-form__label'>
                            @if($index === 0)
                                休憩
                            @else
                                休憩{{ $index + 1 }}
                            @endif
                        </label>
                        <div class='attendance-detail-form__value'>
                            @if($breakRecord->start_time && $breakRecord->end_time)
                                {{ \Carbon\Carbon::parse($breakRecord->start_time)->format('H:i') }}　　~　　　　{{ \Carbon\Carbon::parse($breakRecord->end_time)->format('H:i') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                @foreach($breakStartTimes as $index => $startTime)
                <div class='attendance-detail-form__section'>
                    <div class='attendance-detail-form__field attendance-detail-form__field--horizontal'>
                        <label class='attendance-detail-form__label'>
                            @if($index === 0)
                                休憩
                            @else
                                休憩{{ $index + 1 }}
                            @endif
                        </label>
                        <div class='attendance-detail-form__time-display'>
                            <input type='time'
                                   name='break_start_time[]'
                                   value='{{ $startTime }}'
                                   class='attendance-detail-form__input @error("break_start_time.{$index}") attendance-detail-form__input--error @enderror'
                                   @if(!$attendance->canEdit()) disabled @endif>
                            <span class='attendance-detail-form__time-separator'>~</span>
                            <input type='time'
                                   name='break_end_time[]'
                                   value='{{ $breakEndTimes[$index] ?? "" }}'
                                   class='attendance-detail-form__input @error("break_end_time.{$index}") attendance-detail-form__input--error @enderror'
                                   @if(!$attendance->canEdit()) disabled @endif>
                        </div>
                    </div>
                    @error("break_start_time.{$index}")
                        <div class='attendance-detail-form__error attendance-detail-form__error--block'>{{ $message }}</div>
                    @enderror
                    @error("break_end_time.{$index}")
                        <div class='attendance-detail-form__error attendance-detail-form__error--block'>{{ $message }}</div>
                    @enderror
                </div>
                @endforeach
            @endif

            <div class='attendance-detail-form__section'>
                <div class='attendance-detail-form__field attendance-detail-form__field--horizontal'>
                    <label class='attendance-detail-form__label'>備考</label>
                    @if($attendance->isApproved())
                        <div class='attendance-detail-form__value'>{{ $attendance->remark ?: '-' }}</div>
                    @else
                        <textarea name='remark'
                                  rows='4'
                                  class='attendance-detail-form__textarea @error("remark") attendance-detail-form__textarea--error @enderror'
                                  placeholder='修正理由を記入してください'
                                   @if(!$attendance->canEdit()) disabled @endif>{{ old('remark', $attendance->remark) }}</textarea>
                    @endif
                </div>
                @if(!$attendance->isApproved())
                    @error('remark')
                        <div class='attendance-detail-form__error attendance-detail-form__error--block'>{{ $message }}</div>
                    @enderror
                @endif
            </div>

            @if($attendance->canEdit())
            <div class='attendance-detail-form__actions'>
                <button type='submit' class='attendance-detail-form__submit-button'>
                    修正
                </button>
            </div>
            @endif
        </form>

        @if(!$attendance->canEdit())
            @if($attendance->isApproved())
                <div class='attendance-detail-form__actions' style='text-align: right; margin-top: 1rem;'>
                    <button type='button' class='status-badge status-badge--approved' style='padding: 0.5rem 1rem; font-size: var(--font-size-xl); border: none; background-color: #696969; color: white; border-radius: 4px; cursor: default;'>承認済み</button>
                </div>
            @else
                <div class='attendance-detail-form__error-message'>
                    @if($attendance->work_state === Attendance::WORKING)
                        *勤務中のため修正はできません。退勤完了後に修正が可能です。
                    @elseif($attendance->isPending())
                        *承認待ちのため修正はできません。
                    @elseif($attendance->work_state !== Attendance::AFTER_LEAVE)
                        *退勤完了後の勤怠記録のみ修正が可能です。
                    @endif
                </div>
            @endif
        @endif
    </div>
</div>
@endsection