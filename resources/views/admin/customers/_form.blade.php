<div class="form-grid">
    <label>
        <span>نام</span>
        <input type="text" name="first_name" value="{{ old('first_name', $customer->first_name ?? '') }}" required maxlength="100">
    </label>

    <label>
        <span>نام خانوادگی</span>
        <input type="text" name="last_name" value="{{ old('last_name', $customer->last_name ?? '') }}" required maxlength="100">
    </label>

    <label>
        <span>ایمیل</span>
        <input type="email" name="email" value="{{ old('email', $customer->email ?? '') }}" required autocomplete="off">
    </label>

    <label>
        <span>شماره موبایل</span>
        <input type="tel" name="mobile" value="{{ old('mobile', $customer->mobile ?? '') }}" required inputmode="numeric" placeholder="09123456789">
    </label>

    <label>
        <span>{{ isset($customer) ? 'رمز عبور جدید' : 'رمز عبور' }}</span>
        <input type="password" name="password" @required(! isset($customer)) autocomplete="new-password">
        @isset($customer)<small class="hint">برای حفظ رمز فعلی، خالی بگذارید.</small>@endisset
    </label>

    <label>
        <span>تکرار رمز عبور</span>
        <input type="password" name="password_confirmation" @required(! isset($customer)) autocomplete="new-password">
    </label>

    <label>
        <span>وضعیت</span>
        <select name="status" required>
            @foreach (\App\Enums\RecordStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(old('status', isset($customer) ? $customer->status->value : \App\Enums\RecordStatus::ACTIVE->value) === $status->value)>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
    </label>
</div>

<div class="form-actions">
    <button class="button button-primary" type="submit">{{ $submitLabel }}</button>
    <a class="button button-ghost" href="{{ route('admin.customers.index') }}">انصراف</a>
</div>
