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
            <div class='correction-approve-card__row'>
                <div class='correction-approve-card__label'>休憩</div>
                <div class='correction-approve-card__value'>
                    @if($attendance->breakRecords->count() > 0)
                        @foreach($attendance->breakRecords as $break)
                            {{ \App\Models\AttendanceRecord::normalizeTime($break->start_time) }}　　~　　{{ \App\Models\AttendanceRecord::normalizeTime($break->end_time) }}<br>
                        @endforeach
                    @else
                        ~
                    @endif
                </div>
            </div>
            <div class='correction-approve-card__row'>
                <div class='correction-approve-card__label'>休憩2</div>
                <div class='correction-approve-card__value'> ~ </div>
            </div>
            <div class='correction-approve-card__row'>
                <div class='correction-approve-card__label'>備考</div>
                <div class='correction-approve-card__value'>{{ $attendance->remark ?? 'なし' }}</div>
            </div>
        </div>
    </div>

    <div class='correction-approve-page__actions'>
        <form method='POST' action='{{ route("stamp_correction_request.approve.post", $attendance->id) }}' style='display: inline;'>
            @csrf
            <button type='submit' class='correction-approve-page__button' name='action' value='approve'>承認</button>
        </form>
    </div>
</div>
@endsection
