<div class="form-grid">
    <label>
        <span>مشتری</span>
        <select name="customer_id" required>
            <option value="">انتخاب مشتری</option>
            @foreach ($customers as $customerOption)
                <option value="{{ $customerOption->id }}" @selected((string) old('customer_id', $project->customer_id ?? '') === (string) $customerOption->id)>
                    {{ $customerOption->full_name }} — {{ $customerOption->email }}
                </option>
            @endforeach
        </select>
    </label>

    <label>
        <span>نام پروژه</span>
        <input type="text" name="name" value="{{ old('name', $project->name ?? '') }}" required maxlength="150">
    </label>

    <label class="form-span-2">
        <span>آدرس وب‌سایت</span>
        <input type="url" name="website_url" value="{{ old('website_url', $project->website_url ?? '') }}" required placeholder="https://example.com">
    </label>

    <label>
        <span>وضعیت</span>
        <select name="status" required>
            @foreach (\App\Enums\RecordStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(old('status', isset($project) ? $project->status->value : \App\Enums\RecordStatus::ACTIVE->value) === $status->value)>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
    </label>
</div>

<div class="form-actions">
    <button class="button button-primary" type="submit">{{ $submitLabel }}</button>
    <a class="button button-ghost" href="{{ route('admin.projects.index') }}">انصراف</a>
</div>
