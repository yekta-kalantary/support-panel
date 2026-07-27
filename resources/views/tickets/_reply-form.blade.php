@if ($canReply)
    <section class="card">
        <div class="card-header">
            <h2>ارسال پاسخ</h2>
        </div>

        <form method="post" action="{{ $replyAction }}" enctype="multipart/form-data" class="form-stack">
            @csrf

            <label>
                <span>متن پاسخ</span>
                <textarea name="message" rows="7" required>{{ old('message') }}</textarea>
            </label>

            <label>
                <span>فایل‌های پیوست</span>
                <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.txt,.zip">
                <small class="hint">حداکثر 5 فایل و حداکثر 10 مگابایت برای هر فایل</small>
            </label>

            <button class="button button-primary" type="submit">ارسال پاسخ</button>
        </form>
    </section>
@else
    <div class="alert alert-neutral">این تیکت بسته است و امکان ارسال پاسخ جدید وجود ندارد.</div>
@endif
