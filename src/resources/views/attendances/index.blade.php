@extends('layouts.app')

@section('content')
<div class='attendance-page'>
    {{-- 打刻セクション --}}
    <div class='attendance-clock'>
        {{-- 勤務ステータス --}}
        <div class='attendance-clock__status-badge'>勤務外</div>

        {{-- 日付と時刻 --}}
        <div class='attendance-clock__datetime'>
            <div class='attendance-clock__date'>2023年6月1日(木)</div>
            <div class='attendance-clock__time'>08:00</div>
        </div>

        {{-- 打刻ボタン --}}
        <div class='attendance-clock__actions'>
            <form action='#' method='POST'>
                <button type='submit' class='attendance-clock__button'>出勤</button>
            </form>
        </div>
    </div>
</div>
@endsection
