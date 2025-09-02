@extends('layouts.app')

@section('content')
<div class='register-page'>
    <div class='register-page__body'>
        <h2 class='register-page__title'>会員登録</h2>

        <form method='POST' action='{{ route("register") }}' class='register-form'>
            @csrf

            {{-- Name --}}
            <div class='register-form__group'>
                <label for='name' class='register-form__label'>名前</label>
                <input id='name' type='text' class='register-form__input @error('name') is-invalid @enderror' name='name' value='{{ old('name') }}' required autocomplete='name' autofocus>
                @error('name')
                    <span class='register-form__error' role='alert'>
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- Email Address --}}
            <div class='register-form__group'>
                <label for='email' class='register-form__label'>メールアドレス</label>
                <input id='email' type='email' class='register-form__input @error('email') is-invalid @enderror' name='email' value='{{ old('email') }}' required autocomplete='email'>
                @error('email')
                    <span class='register-form__error' role='alert'>
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- Password --}}
            <div class='register-form__group'>
                <label for='password' class='register-form__label'>パスワード</label>
                <input id='password' type='password' class='register-form__input @error('password') is-invalid @enderror' name='password' required autocomplete='new-password'>
                @error('password')
                    <span class='register-form__error' role='alert'>
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class='register-form__group'>
                <label for='password-confirm' class='register-form__label'>パスワード確認</label>
                <input id='password-confirm' type='password' class='register-form__input' name='password_confirmation' required autocomplete='new-password'>
            </div>

            <div class='register-form__actions'>
                <button type='submit' class='register-form__button'>
                    登録する
                </button>
            </div>
        </form>

        <div class='register-form__links'>
            <a class='register-form__link' href='{{ route('login') }}'>
                ログインはこちら
            </a>
        </div>
    </div>
</div>
@endsection
