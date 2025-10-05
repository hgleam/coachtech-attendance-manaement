@extends('layouts.admin')

@section('content')
<div class='staff-attendance-page'>
    @if (isset($errorMessage) && $errorMessage)
        <div class="alert alert-danger">
            {{ $errorMessage }}
        </div>
    @endif

    <div class='staff-attendance-page__header'>
        <h2 class='staff-attendance-page__title'>{{ $user->name }}さんの勤怠</h2>
    </div>

    <div class='month-navigator'>
        <a href='{{ route("admin.attendance.staff", ["id" => $user->id, "month" => $prevMonth->format("Y-m")]) }}' class='month-navigator__link'>&larr; 前月</a>
        <span class='month-navigator__current'>
            <i class='month-navigator__icon'>📅</i>
            {{ $currentMonth->format('Y/m') }}
        </span>
        <a href='{{ route("admin.attendance.staff", ["id" => $user->id, "month" => $nextMonth->format("Y-m")]) }}' class='month-navigator__link'>翌月 &rarr;</a>
    </div>

    <div class='staff-attendance-card'>
        <table class='staff-attendance-card__table'>
            <thead class='staff-attendance-card__header'>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody class='staff-attendance-card__body'>
                @foreach($attendanceData as $data)
                <tr>
                    <td>{{ $data['date']->format('m/d') }}({{ ['日', '月', '火', '水', '木', '金', '土'][$data['date']->dayOfWeek] }})</td>
                    <td>{{ $data['clock_in_time'] }}</td>
                    <td>{{ $data['clock_out_time'] }}</td>
                    <td>{{ $data['break_time'] }}</td>
                    <td>{{ $data['total_work_time'] }}</td>
                    <td>
                        @if($data['attendance'])
                            <a href='{{ route("admin.attendance.show", $data["attendance"]->id) }}' class='staff-attendance-card__detail-link'>詳細</a>
                        @else
                            <span>-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class='staff-attendance-page__actions'>
        <a href='{{ route("admin.attendance.staff.csv", ["id" => $user->id, "year" => $currentMonth->year, "month" => $currentMonth->month]) }}' class='staff-attendance-page__csv-button'>CSV出力</a>
    </div>
</div>
@endsection
