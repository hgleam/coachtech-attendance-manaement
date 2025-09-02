@extends('layouts.app')

@section('content')
<div class='staff-attendance-page'>
    <div class='staff-attendance-page__header'>
        <h2 class='staff-attendance-page__title'>西玲奈さんの勤怠</h2>
    </div>

    <div class='month-navigator'>
        <a href='#' class='month-navigator__link'>&larr; 前月</a>
        <span class='month-navigator__current'>
            <i class='month-navigator__icon'>📅</i>
            2023/06
        </span>
        <a href='#' class='month-navigator__link'>翌月 &rarr;</a>
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
                {{-- ダミーデータ --}}
                <tr>
                    <td>06/01(木)</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><a href='#' class='staff-attendance-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>06/02(金)</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><a href='#' class='staff-attendance-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>06/03(土)</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><a href='#' class='staff-attendance-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>06/04(日)</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><a href='#' class='staff-attendance-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>06/05(月)</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><a href='#' class='staff-attendance-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>06/06(火)</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><a href='#' class='staff-attendance-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>06/07(水)</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><a href='#' class='staff-attendance-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>06/08(木)</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><a href='#' class='staff-attendance-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>06/09(金)</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><a href='#' class='staff-attendance-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>06/10(土)</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><a href='#' class='staff-attendance-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>06/11(日)</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><a href='#' class='staff-attendance-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>06/12(月)</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><a href='#' class='staff-attendance-card__detail-link'>詳細</a></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class='staff-attendance-page__actions'>
        <button type='button' class='staff-attendance-page__csv-button'>CSV出力</button>
    </div>
</div>
@endsection
