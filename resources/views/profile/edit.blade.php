@extends('layouts.app')

@section('title', 'پروفایل')
@section('heading', 'پروفایل')
@section('description', 'اطلاعات حساب و تغییر رمز عبور')

@section('content')
<div class="grid grid-2">
    <section class="card">
        <div class="card-header">
            <h2>اطلاعات حساب</h2>
        </div>
        <dl class="details">
            <div><dt>نام و نام خانوادگی</dt><dd>{{ $user->full_name }}</dd></div>
            <div><dt>ایمیل</dt><dd>{{ $user->email }}</dd></div>
            <div><dt>شماره موبایل</dt><dd>{{ $user->mobile }}</dd></div>
            <div><dt>نقش</dt><dd>{{ $user->role->label() }}</dd></div>
            <div><dt>وضعیت</dt><dd><x-status-badge :status="$user->status" /></dd></div>
        </dl>
    </section>

    <section class="card">
        <div class="card-header">
            <h2>تغییر رمز عبور</h2>
        </div>
        <form method="post" action="{{ route('profile.password.update') }}" class="form-stack">
            @csrf
            @method('PUT')

            <label>
                <span>رمز عبور فعلی</span>
                <input type="password" name="current_password" required autocomplete="current-password">
            </label>
            <label>
                <span>رمز عبور جدید</span>
                <input type="password" name="password" required autocomplete="new-password">
            </label>
            <label>
                <span>تکرار رمز عبور جدید</span>
                <input type="password" name="password_confirmation" required autocomplete="new-password">
            </label>

            <button class="button button-primary" type="submit">تغییر رمز عبور</button>
        </form>
    </section>
</div>
@endsection
