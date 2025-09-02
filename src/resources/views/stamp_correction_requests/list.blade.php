@extends('layouts.app')

@section('content')
<div class='correction-request-list-page'>
    <div class='correction-request-list-page__header'>
        <h2 class='correction-request-list-page__title'>申請一覧</h2>
    </div>

    <div class='request-tabs'>
        <a href='#' class='request-tabs__item request-tabs__item--active'>承認待ち</a>
        <a href='#' class='request-tabs__item request-tabs__item--inactive'>承認済み</a>
    </div>

    <div class='request-list-card'>
        <table class='request-list-card__table'>
            <thead class='request-list-card__header'>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody class='request-list-card__body'>
                {{-- ダミーデータ --}}
                <tr>
                    <td><span class='status-badge status-badge--pending'>承認待ち</span></td>
                    <td>西伶奈</td>
                    <td>2023/06/01</td>
                    <td>遅延のため</td>
                    <td>2023/06/02</td>
                    <td><a href='#' class='request-list-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td><span class='status-badge status-badge--pending'>承認待ち</span></td>
                    <td>西伶奈</td>
                    <td>2023/06/01</td>
                    <td>遅延のため</td>
                    <td>2023/06/02</td>
                    <td><a href='#' class='request-list-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td><span class='status-badge status-badge--pending'>承認待ち</span></td>
                    <td>西伶奈</td>
                    <td>2023/06/01</td>
                    <td>遅延のため</td>
                    <td>2023/06/02</td>
                    <td><a href='#' class='request-list-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td><span class='status-badge status-badge--pending'>承認待ち</span></td>
                    <td>西伶奈</td>
                    <td>2023/06/01</td>
                    <td>遅延のため</td>
                    <td>2023/06/02</td>
                    <td><a href='#' class='request-list-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td><span class='status-badge status-badge--pending'>承認待ち</span></td>
                    <td>西伶奈</td>
                    <td>2023/06/01</td>
                    <td>遅延のため</td>
                    <td>2023/06/02</td>
                    <td><a href='#' class='request-list-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td><span class='status-badge status-badge--pending'>承認待ち</span></td>
                    <td>西伶奈</td>
                    <td>2023/06/01</td>
                    <td>遅延のため</td>
                    <td>2023/06/02</td>
                    <td><a href='#' class='request-list-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td><span class='status-badge status-badge--pending'>承認待ち</span></td>
                    <td>西伶奈</td>
                    <td>2023/06/01</td>
                    <td>遅延のため</td>
                    <td>2023/06/02</td>
                    <td><a href='#' class='request-list-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td><span class='status-badge status-badge--pending'>承認待ち</span></td>
                    <td>西伶奈</td>
                    <td>2023/06/01</td>
                    <td>遅延のため</td>
                    <td>2023/06/02</td>
                    <td><a href='#' class='request-list-card__detail-link'>詳細</a></td>
                </tr>
                <tr>
                    <td><span class='status-badge status-badge--pending'>承認待ち</span></td>
                    <td>西伶奈</td>
                    <td>2023/06/01</td>
                    <td>遅延のため</td>
                    <td>2023/06/02</td>
                    <td><a href='#' class='request-list-card__detail-link'>詳細</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
