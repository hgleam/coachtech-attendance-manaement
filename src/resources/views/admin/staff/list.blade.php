@extends('layouts.admin')

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
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><a href='{{ route("admin.attendance.staff", $user->id) }}' class='staff-list-card__detail-link'>詳細</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
