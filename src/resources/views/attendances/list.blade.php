@extends('layouts.app')

@section('content')
<div class='attendance-list-page'>
    <div class='attendance-list-page__header'>
        <h2 class='attendance-list-page__title'>{{ $currentMonth->format('Y年n月') }}の勤怠</h2>
    </div>

    <div class='month-navigator'>
        <a href='{{ route("attendance.list", ["month" => $prevMonth]) }}' class='month-navigator__link'>&larr; 前月</a>
        <span class='month-navigator__current'>{{ $currentMonth->format('Y年n月') }}</span>
        <a href='{{ route("attendance.list", ["month" => $nextMonth]) }}' class='month-navigator__link'>翌月 &rarr;</a>
    </div>

    <div class='attendance-list-card'>
        <table class='attendance-list-card__table'>
            <thead class='attendance-list-card__header'>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody class='attendance-list-card__body'>
                @foreach ($attendanceData as $data)
                <tr>
                    <td>{{ $data['date']->format('m/d') }}({{ ['日', '月', '火', '水', '木', '金', '土'][$data['date']->dayOfWeek] }})</td>
                    <td>{{ $data['attendance'] ? $data['attendance']->clock_in_time : '' }}</td>
                    <td>{{ $data['attendance'] ? $data['attendance']->clock_out_time : '' }}</td>
                    <td>{{ $data['break_time'] ?? '' }}</td>
                    <td>{{ $data['total_work_time'] ?? '' }}</td>
                    <td>
                        @if ($data['attendance'])
                            <a href='{{ route("attendance.show", $data["attendance"]->id) }}' class='attendance-list-card__detail-link'>詳細</a>
                        @else
                            <span class='attendance-list-card__empty-text'>-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
