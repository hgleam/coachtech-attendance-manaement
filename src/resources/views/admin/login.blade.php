@extends('layouts.app')

@section('content')
<div class='login-page'>
    <div class='login-page__body'>
        <h2 class='login-page__title'>管理者ログイン</h2>

        <form method='POST' action='{{ route("admin.login") }}' class='login-form' novalidate>
            @csrf

            <div class='login-form__group'>
                <label for='email' class='login-form__label'>メールアドレス</label>
                <input id='email' type='email' class='login-form__input @error('email') is-invalid @enderror' name='email' value='{{ old('email') }}' required autocomplete='email' autofocus>
                @error('email')
                    <span class='login-form__error' role='alert'>
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class='login-form__group'>
                <label for='password' class='login-form__label'>パスワード</label>
                <input id='password' type='password' class='login-form__input @error('password') is-invalid @enderror' name='password' required autocomplete='current-password'>
                @error('password')
                    <span class='login-form__error' role='alert'>
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class='login-form__actions'>
                <button type='submit' class='login-form__button'>
                    管理者ログインする
                </button>
            </div>
        </form>
    </div>
</div>
@endsection