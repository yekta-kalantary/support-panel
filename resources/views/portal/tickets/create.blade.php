@extends('layouts.app')

@section('title', 'ثبت تیکت')
@section('heading', 'ثبت تیکت جدید')
@section('description', 'درخواست خود را برای یکی از پروژه‌های فعال ثبت کنید')

@section('content')
<section class="card">
    @if ($projects->isEmpty())
        <x-empty-state message="پروژه فعال برای ثبت تیکت ندارید." />
    @else
        <form method="post" action="{{ route('portal.tickets.store') }}" enctype="multipart/form-data" class="form-stack">
            @csrf

            <label>
                <span>پروژه</span>
                <select name="project_id" required>
                    <option value="">انتخاب پروژه</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @selected((string) old('project_id', $selectedProjectId) === (string) $project->id)>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>
                <span>عنوان تیکت</span>
                <input type="text" name="subject" value="{{ old('subject') }}" required minlength="5" maxlength="200">
            </label>

            <label>
                <span>شرح درخواست</span>
                <textarea name="message" rows="10" required minlength="10">{{ old('message') }}</textarea>
            </label>

            <label>
                <span>فایل‌های پیوست</span>
                <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.txt,.zip">
                <small class="hint">حداکثر 5 فایل و حداکثر 10 مگابایت برای هر فایل</small>
            </label>

            <div class="form-actions">
                <button class="button button-primary" type="submit">ثبت تیکت</button>
                <a class="button button-ghost" href="{{ route('portal.tickets.index') }}">انصراف</a>
            </div>
        </form>
    @endif
</section>
@endsection
