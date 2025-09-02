@extends('layouts.app')

@section('content')
<div class='staff-list-page'>
    <div class='staff-list-page__header'>
        <h2 class='staff-list-page__title'>スタッフ一覧</h2>
    </div>

    <div class='staff-list-card'>
        <table class='staff-list-card__table'>
            <thead class='staff-list-card__header'>
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>月次勤怠</th>
                </tr>
            </thead>
            <tbody class='staff-list-card__body'>
                {{-- ダミーデータ --}}
                <tr>
                    <td>西 伶奈</td>
                    <td>reina.n@coachtech.com</td>
                    <td><a href='#' class='staff-list-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>山田 太郎</td>
                    <td>taro.y@coachtech.com</td>
                    <td><a href='#' class='staff-list-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>増田 一世</td>
                    <td>issei.m@coachtech.com</td>
                    <td><a href='#' class='staff-list-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>山本 敬吉</td>
                    <td>keikichi.y@coachtech.com</td>
                    <td><a href='#' class='staff-list-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>秋田 朋美</td>
                    <td>tomomi.a@coachtech.com</td>
                    <td><a href='#' class='staff-list-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td>中西 教夫</td>
                    <td>norio.n@coachtech.com</td>
                    <td><a href='#' class='staff-list-card__detail-link'>詳細</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
