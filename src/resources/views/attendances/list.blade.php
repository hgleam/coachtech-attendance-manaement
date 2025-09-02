@extends('layouts.app')

@section('content')
<div class='attendance-list-page'>
    <div class='attendance-list-page__header'>
        <h2 class='attendance-list-page__title'>勤怠一覧</h2>
    </div>

    <div class='month-navigator'>
        <a href='#' class='month-navigator__link'>&larr; 前月</a>
        <span class='month-navigator__current'>2023/06</span>
        <a href='#' class='month-navigator__link'>翌月 &rarr;</a>
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
                {{-- ダミーデータ --}}
                @for ($i = 1; $i <= 12; $i++)
                <tr>
                    <td>06/{{ sprintf('%02d', $i) }}(月)</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><a href='#'>詳細</a></td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>
@endsection
