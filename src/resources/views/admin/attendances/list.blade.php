@extends('layouts.admin')

@section('content')
<div class='attendance-list-page'>
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif
    <div class='attendance-list-page__header'>
        <h2 class='attendance-list-page__title'>{{ $currentDate->format('Y年n月j日') }}の勤怠</h2>
    </div>

    <div class='month-navigator'>
        <a href='{{ route("admin.attendance.list", ["date" => $prevDate]) }}' class='month-navigator__link'>&larr; 前日</a>
        <span class='month-navigator__current'>
            <span class='calendar-icon' onclick='openDatePicker()'>📅</span>
            {{ $currentDate->format('Y年n月j日') }}
            <input type='date' id='date-picker' value='{{ $currentDate->format("Y-m-d") }}' class='date-picker-hidden'>
        </span>
        <a href='{{ route("admin.attendance.list", ["date" => $nextDate]) }}' class='month-navigator__link'>翌日 &rarr;</a>
    </div>

    <div class='attendance-list-card'>
        <table class='attendance-list-card__table'>
            <thead class='attendance-list-card__header'>
                <tr>
                    <th>名前</th>
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
                    <td>{{ $data['user']->name }}</td>
                    <td>{{ $data['clock_in_time'] ?? '' }}</td>
                    <td>{{ $data['clock_out_time'] ?? '' }}</td>
                    <td>{{ $data['break_time'] ?? '' }}</td>
                    <td>{{ $data['total_work_time'] ?? '' }}</td>
                    <td>
                        @if ($data['attendance'])
                            <a href='{{ route("admin.attendance.show", $data["attendance"]->id) }}'>詳細</a>
                        @else
                            <span>-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
// 日付ピッカーの開閉
function openDatePicker() {
    const datePicker = document.getElementById('date-picker');
    const calendarIcon = document.querySelector('.calendar-icon');

    // カレンダーアイコンの位置を取得
    const rect = calendarIcon.getBoundingClientRect();

    // 日付入力フィールドをカレンダーアイコンの位置に配置
    datePicker.style.position = 'fixed';
    datePicker.style.left = rect.left + 'px';
    datePicker.style.top = rect.top + 'px';
    datePicker.style.zIndex = '9999';
    datePicker.style.opacity = '0';
    datePicker.style.pointerEvents = 'auto';
    datePicker.style.width = '1px';
    datePicker.style.height = '1px';

    // カレンダーを開く
    datePicker.showPicker();
}

// 日付ピッカーの変更時に勤怠一覧画面に遷移
document.addEventListener('DOMContentLoaded', function() {
    const datePicker = document.getElementById('date-picker');

    datePicker.addEventListener('change', function() {
        const selectedDate = this.value;
        if (selectedDate) {
            window.location.href = '{{ route("admin.attendance.list") }}?date=' + selectedDate;
        }
    });
});
</script>

@endsection