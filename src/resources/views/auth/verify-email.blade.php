@extends('layouts.email-verification')

@section('content')
<div class='email-verification-page'>
    <div class='email-verification-message'>
        <p>登録していただいたメールアドレスに認証メールを送付しました。</p>
        <p>メール認証を完了してください。</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class='email-verification-success'>
            <p>新しい認証リンクを送信しました。</p>
        </div>
    @endif

    <div class='email-verification-actions'>
        <a href="#" class='email-verification-button'>
            認証はこちらから
        </a>
    </div>

    <div class='email-verification-resend'>
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class='email-verification-resend-link'>
                認証メールを再送する
            </button>
        </form>
    </div>
</div>
@endsection