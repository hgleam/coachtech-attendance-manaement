@extends('layouts.app')

@section('content')
<div class='correction-request-list-page'>
    <div class='correction-request-list-page__header'>
        <h2 class='correction-request-list-page__title'>申請一覧</h2>
    </div>

    <div class='request-tabs'>
        <a href='#pending' class='request-tabs__item request-tabs__item--active' onclick='showTab("pending")'>承認待ち</a>
        <a href='#approved' class='request-tabs__item request-tabs__item--inactive' onclick='showTab("approved")'>承認済み</a>
    </div>

    {{-- 承認待ちタブ --}}
    <div id='pending-tab' class='request-list-card'>
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
                @forelse($pendingRequests as $request)
                <tr>
                    <td><span class='status-badge status-badge--pending'>承認待ち</span></td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ $request->date->format('Y/m/d') }}</td>
                    <td>{{ $request->correction_reason ?: $request->remark }}</td>
                    <td>{{ $request->applied_at ? $request->applied_at->format('Y/m/d') : '-' }}</td>
                    <td><a href='{{ route("admin.stamp_correction_request.approve", $request->id) }}' class='request-list-card__detail-link'>詳細</a></td>
                </tr>
                @empty
                <tr>
                    <td colspan='6' style='text-align: center; padding: 2rem;'>承認待ちの申請はありません</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- 承認済みタブ --}}
    <div id='approved-tab' class='request-list-card' style='display: none;'>
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
                @forelse($approvedRequests as $request)
                <tr>
                    <td><span class='status-badge status-badge--approved'>承認済み</span></td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ $request->date->format('Y/m/d') }}</td>
                    <td>{{ $request->correction_reason ?: $request->remark }}</td>
                    <td>{{ $request->applied_at ? $request->applied_at->format('Y/m/d') : '-' }}</td>
                    <td><a href='{{ route("admin.stamp_correction_request.approve", $request->id) }}' class='request-list-card__detail-link'>詳細</a></td>
                </tr>
                @empty
                <tr>
                    <td colspan='6' style='text-align: center; padding: 2rem;'>承認済みの申請はありません</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        function showTab(tabName) {
            // タブの表示切り替え
            document.getElementById('pending-tab').style.display = tabName === 'pending' ? 'block' : 'none';
            document.getElementById('approved-tab').style.display = tabName === 'approved' ? 'block' : 'none';

            // タブのアクティブ状態切り替え
            const tabs = document.querySelectorAll('.request-tabs__item');
            tabs.forEach(tab => {
                tab.classList.remove('request-tabs__item--active');
                tab.classList.add('request-tabs__item--inactive');
            });

            const activeTab = document.querySelector(`a[href="#${tabName}"]`);
            if (activeTab) {
                activeTab.classList.remove('request-tabs__item--inactive');
                activeTab.classList.add('request-tabs__item--active');
            }
        }
    </script>
</div>
@endsection
