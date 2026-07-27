<x-layouts.guest title="بازیابی رمز عبور">
    <h1 class="guest-title">بازیابی رمز عبور</h1>
    <p class="muted">ایمیل حساب را وارد کنید تا لینک بازیابی برای شما ارسال شود.</p>

    <form method="post" action="{{ route('password.email') }}" class="form-stack">
        @csrf
        <label>
            <span>ایمیل</span>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
        </label>

        <button class="button button-primary button-block" type="submit">ارسال لینک بازیابی</button>
        <a class="text-link center" href="{{ route('login') }}">بازگشت به ورود</a>
    </form>
</x-layouts.guest>
