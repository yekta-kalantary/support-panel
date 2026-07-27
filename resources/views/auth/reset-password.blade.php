<x-layouts.guest title="تغییر رمز عبور">
    <h1 class="guest-title">تغییر رمز عبور</h1>

    <form method="post" action="{{ route('password.update') }}" class="form-stack">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <label>
            <span>ایمیل</span>
            <input type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email">
        </label>

        <label>
            <span>رمز عبور جدید</span>
            <input type="password" name="password" required autocomplete="new-password">
        </label>

        <label>
            <span>تکرار رمز عبور جدید</span>
            <input type="password" name="password_confirmation" required autocomplete="new-password">
        </label>

        <button class="button button-primary button-block" type="submit">ثبت رمز عبور جدید</button>
    </form>
</x-layouts.guest>
