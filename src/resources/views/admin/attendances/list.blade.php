@extends('layouts.app')

@section('content')
<div class='admin-attendance-list-page'>
    <div class='admin-attendance-list-page__header'>
        <h2 class='admin-attendance-list-page__title'>2023年6月1日の勤怠</h2>
    </div>

    <div class='date-navigator'>
        <a href='#' class='date-navigator__link'>&larr; 前日</a>
        <span class='date-navigator__current'>
            <i class='date-navigator__icon'>📅</i>
            2023/06/01
        </span>
        <a href='#' class='date-navigator__link'>翌日 &rarr;</a>
    </div>

    <div class='admin-attendance-list-card'>
        <table class='admin-attendance-list-card__table'>
            <thead class='admin-attendance-list-card__header'>
                <tr>
                    <th>名前</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody class='admin-attendance-list-card__body'>
                {{-- ダミーデータ --}}
                <tr>
                    <td>山田 太郎</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><a href='#' class='admin-attendance-list-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>西 伶奈</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><a href='#' class='admin-attendance-list-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>増田 一世</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><a href='#' class='admin-attendance-list-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>山本 敬吉</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><a href='#' class='admin-attendance-list-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>秋田 朋美</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><a href='#' class='admin-attendance-list-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>中西 教夫</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><a href='#' class='admin-attendance-list-card__detail-link'>詳細</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
