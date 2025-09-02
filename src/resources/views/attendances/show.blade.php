@extends('layouts.app')

@section('content')
<div class='attendance-detail-page'>
    <div class='attendance-detail-page__header'>
        <h2 class='attendance-detail-page__title'>勤怠詳細</h2>
    </div>

    <div class='detail-card'>
        <form action='#' method='POST' class='detail-form'>
            <div class='detail-form__row'>
                <div class='detail-form__label'>名前</div>
                <div class='detail-form__value'>西 伶奈</div>
            </div>
            <div class='detail-form__row'>
                <div class='detail-form__label'>日付</div>
                <div class='detail-form__value-group'>
                    <span>2023年</span>
                    <span>6月1日</span>
                </div>
            </div>
            <div class='detail-form__row'>
                <div class='detail-form__label'>出勤・退勤</div>
                <div class='detail-form__value-group'>
                    <input type='text' class='detail-form__input' value='09:00'>
                    <span class='detail-form__separator'>~</span>
                    <input type='text' class='detail-form__input' value='18:00'>
                </div>
            </div>
            <div class='detail-form__row'>
                <div class='detail-form__label'>休憩</div>
                <div class='detail-form__value-group'>
                    <input type='text' class='detail-form__input' value='12:00'>
                    <span class='detail-form__separator'>~</span>
                    <input type='text' class='detail-form__input' value='13:00'>
                </div>
            </div>
            <div class='detail-form__row'>
                <div class='detail-form__label'>休憩2</div>
                <div class='detail-form__value-group'>
                    <input type='text' class='detail-form__input'>
                    <span class='detail-form__separator'>~</span>
                    <input type='text' class='detail-form__input'>
                </div>
            </div>
            <div class='detail-form__row'>
                <div class='detail-form__label'>備考</div>
                <div class='detail-form__value'>
                    <textarea class='detail-form__textarea'>電車遅延のため</textarea>
                </div>
            </div>
            <div class='detail-form__actions'>
                <button type='submit' class='detail-form__button'>修正</button>
            </div>
        </form>
    </div>
</div>
@endsection
