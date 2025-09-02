@extends('layouts.app')

@section('content')
<div class='correction-approve-page'>
    <div class='correction-approve-page__header'>
        <h2 class='correction-approve-page__title'>勤怠詳細</h2>
    </div>

    <div class='correction-approve-card'>
        <div class='correction-approve-card__body'>
            <div class='correction-approve-card__row'>
                <div class='correction-approve-card__label'>名前</div>
                <div class='correction-approve-card__value'>西　伶奈</div>
            </div>
            <div class='correction-approve-card__row'>
                <div class='correction-approve-card__label'>日付</div>
                <div class='correction-approve-card__value'>2023年　　6月1日</div>
            </div>
            <div class='correction-approve-card__row'>
                <div class='correction-approve-card__label'>出勤・退勤</div>
                <div class='correction-approve-card__value'>09:00　　~　　18:00</div>
            </div>
            <div class='correction-approve-card__row'>
                <div class='correction-approve-card__label'>休憩</div>
                <div class='correction-approve-card__value'>12:00　　~　　13:00</div>
            </div>
            <div class='correction-approve-card__row'>
                <div class='correction-approve-card__label'>休憩2</div>
                <div class='correction-approve-card__value'> ~ </div>
            </div>
            <div class='correction-approve-card__row'>
                <div class='correction-approve-card__label'>備考</div>
                <div class='correction-approve-card__value'>電車遅延のため</div>
            </div>
        </div>
    </div>

    <div class='correction-approve-page__actions'>
        <button type='button' class='correction-approve-page__button'>承認</button>
    </div>
</div>
@endsection
