@extends('layouts.app')

@section('content')
<div class='verify-page'>
    <div class='verify-page__body'>
        <p class='verify-page__message'>
            ご登録いただきありがとうございます。<br>
            ご入力いただいたメールアドレスへ認証リンクを送信しましたので、ご確認ください。
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class='verify-page__success'>
                新しい認証リンクがメールアドレスに送信されました。
            </div>
        @endif

        <div class='verify-form'>
            <div class='verify-form__group'>
                <form method='POST' action='{{ route("verification.send") }}'>
                    @csrf
                    <button type='submit' class='verify-form__button'>
                        認証メールを再送信
                    </button>
                </form>
            </div>

            <div class='verify-form__links'>
                <form method='POST' action='{{ route("logout") }}'>
                    @csrf
                    <button type='submit' class='verify-form__link'>
                        ログアウト
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
