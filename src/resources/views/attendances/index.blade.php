@php
use App\Constants\Attendance;
@endphp

@extends('layouts.app')

@section('content')
<div class='attendance-page'>
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class='attendance-clock'>
        <div class='attendance-clock__status-badge'>{{ $workStateDisplay }}</div>

        <div class='attendance-clock__datetime'>
            <div class='attendance-clock__date'>{{ $currentTime->format('Y年n月j日') }}({{ ['日', '月', '火', '水', '木', '金', '土'][$currentTime->dayOfWeek] }})</div>
            <div class='attendance-clock__time'>{{ $currentTime->format('H:i') }}</div>
        </div>

        <div class='attendance-clock__actions'>
            @if ($currentWorkState === Attendance::BEFORE_WORK)
                <form action="{{ route('attendance.clock-in') }}" method='POST'>
                    @csrf
                    <button type='submit' class='attendance-clock__button'>出勤</button>
                </form>
            @elseif ($currentWorkState === Attendance::WORKING)
                <form action="{{ route('attendance.clock-out') }}" method='POST' style="display: inline-block; margin-right: 30px;">
                    @csrf
                    <button type='submit' class='attendance-clock__button'>退勤</button>
                </form>
                <form action="{{ route('attendance.break-start') }}" method='POST' style="display: inline-block;">
                    @csrf
                    <button type='submit' class='attendance-clock__button attendance-clock__button--break'>休憩入</button>
                </form>
            @elseif ($currentWorkState === Attendance::ON_BREAK)
                <form action="{{ route('attendance.break-end') }}" method='POST'>
                    @csrf
                    <button type='submit' class='attendance-clock__button attendance-clock__button--break'>休憩戻</button>
                </form>
            @elseif ($currentWorkState === Attendance::AFTER_LEAVE)
                <p class="attendance-clock__end-message">お疲れ様でした。</p>
            @endif
        </div>
    </div>
</div>
@endsection
