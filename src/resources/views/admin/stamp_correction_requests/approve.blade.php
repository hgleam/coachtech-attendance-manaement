@php
use App\Models\AttendanceRecord;
@endphp

@extends('layouts.admin')

@section('content')
<div class='correction-approve-page'>
    <div class='correction-approve-page__header'>
        <h2 class='correction-approve-page__title'>勤怠詳細</h2>
    </div>

    <div class='correction-approve-card'>
        <div class='correction-approve-card__body'>
            <div class='correction-approve-card__row'>
                <div class='correction-approve-card__label'>名前</div>
                <div class='correction-approve-card__value'>{{ $attendance->user->name ?? '不明' }}</div>
            </div>
            <div class='correction-approve-card__row'>
                <div class='correction-approve-card__label'>日付</div>
                <div class='correction-approve-card__value'>{{ $attendance->date->format('Y年　　n月j日') }}</div>
            </div>
            <div class='correction-approve-card__row'>
                <div class='correction-approve-card__label'>出勤・退勤</div>
                <div class='correction-approve-card__value'>
                    {{ $attendance->getFormattedClockInTime() }}　　~　　{{ $attendance->getFormattedClockOutTime() }}
                </div>
            </div>
            @if($attendance->breakRecords->count() > 0)
                @foreach($attendance->breakRecords as $index => $break)
                    <div class='correction-approve-card__row'>
                        <div class='correction-approve-card__label'>
                            @if($index === 0)
                                休憩
                            @else
                                休憩{{ $index + 1 }}
                            @endif
                        </div>
                        <div class='correction-approve-card__value'>
                            {{ $break->start_time->format('H:i') }}　　~　　{{ $break->end_time->format('H:i') }}
                        </div>
                    </div>
                @endforeach
            @else
                <div class='correction-approve-card__row'>
                    <div class='correction-approve-card__label'>休憩</div>
                    <div class='correction-approve-card__value'>~</div>
                </div>
            @endif
            <div class='correction-approve-card__row'>
                <div class='correction-approve-card__label'>備考</div>
                <div class='correction-approve-card__value'>{{ $attendance->remark ?? 'なし' }}</div>
            </div>
        </div>
    </div>

    <div class='correction-approve-page__actions'>
        @if($attendance->approval_status === \App\Constants\Attendance::APPROVED)
            <button type='button' class='correction-approve-page__button correction-approve-page__button--approved' disabled>承認済み</button>
        @else
            <form method='POST' action='{{ route("stamp_correction_request.approve.post", $attendance->id) }}' style='display: inline;'>
                @csrf
                <button type='submit' class='correction-approve-page__button' name='action' value='approve'>承認</button>
            </form>
        @endif
    </div>
</div>
@endsection
