@extends('layouts.app')

@section('content')
<div class='admin-request-list-page'>
    <h2 class='admin-request-list-page__title'>修正申請一覧</h2>

    <div class='admin-request-list-card'>
        <table class='admin-request-list-card__table'>
            <thead class='admin-request-list-card__header'>
                <tr>
                    <th>名前</th>
                    <th>申請日</th>
                    <th>修正項目</th>
                    <th>修正時間</th>
                    <th>ステータス</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class='admin-request-list-card__body'>
                {{-- ダミーデータ --}}
                @php
                    $names = ['山田 太郎', '鈴木 花子', '佐藤 次郎'];
                    $items = ['出勤時間', '退勤時間'];
                    $statuses = [
                        ['label' => '承認待ち', 'class' => 'pending'],
                        ['label' => '承認済み', 'class' => 'approved'],
                        ['label' => '却下', 'class' => 'rejected']
                    ];
                @endphp
                @for ($i = 0; $i < 5; $i++)
                <tr>
                    <td>{{ $names[$i % 3] }}</td>
                    <td>2024-07-{{ 26 - $i }}</td>
                    <td>{{ $items[$i % 2] }}</td>
                    <td>{{ $items[$i % 2] === '出勤時間' ? '09:05:00' : '18:30:00' }}</td>
                    <td><span class='status-badge status-badge--{{ $statuses[$i % 3]['class'] }}'>{{ $statuses[$i % 3]['label'] }}</span></td>
                    <td><a href='#'>詳細</a></td>
                </tr>
                @endfor
            </tbody>
        </table>

        {{-- ページネーション --}}
        <div class='pagination'>
            <a href='#' class='pagination__link pagination__link--disabled'>&laquo;</a>
            <a href='#' class='pagination__link pagination__link--active'>1</a>
            <a href='#' class='pagination__link'>2</a>
            <a href='#' class='pagination__link'>3</a>
            <a href='#' class='pagination__link'>&raquo;</a>
        </div>
    </div>
</div>
@endsection
