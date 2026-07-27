<x-layouts.guest title="ورود">
    <h1 class="guest-title">ورود به حساب کاربری</h1>
    <p class="muted">برای مشاهده پروژه‌ها و تیکت‌ها وارد شوید.</p>

    <form method="post" action="{{ route('login.store') }}" class="form-stack">
        @csrf
        <label>
            <span>ایمیل</span>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
        </label>

        <label>
            <span>رمز عبور</span>
            <input type="password" name="password" required autocomplete="current-password">
        </label>

        <label class="checkbox-row">
            <input type="checkbox" name="remember" value="1">
            <span>مرا به خاطر بسپار</span>
        </label>

        <button class="button button-primary button-block" type="submit">ورود</button>
        <a class="text-link center" href="{{ route('password.request') }}">رمز عبور را فراموش کرده‌اید؟</a>
    </form>
</x-layouts.guest>
