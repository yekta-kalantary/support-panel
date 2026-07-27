@extends('layouts.app')

@section('title', 'پروژه‌ها')
@section('heading', 'پروژه‌ها')
@section('description', 'مدیریت پروژه‌های مشتریان')

@section('content')
<div class="page-actions">
    <form method="get" class="filters">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="نام پروژه، سایت یا مشتری">
        <select name="customer_id">
            <option value="">همه مشتریان</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" @selected((string) request('customer_id') === (string) $customer->id)>{{ $customer->full_name }}</option>
            @endforeach
        </select>
        <select name="status">
            <option value="">همه وضعیت‌ها</option>
            @foreach (\App\Enums\RecordStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <button class="button button-secondary" type="submit">فیلتر</button>
        <a class="button button-ghost" href="{{ route('admin.projects.index') }}">پاک کردن</a>
    </form>
    <a class="button button-primary" href="{{ route('admin.projects.create') }}">پروژه جدید</a>
</div>

<section class="card">
    @if ($projects->isEmpty())
        <x-empty-state message="پروژه‌ای پیدا نشد." />
    @else
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>نام پروژه</th>
                    <th>مشتری</th>
                    <th>وب‌سایت</th>
                    <th>تیکت‌ها</th>
                    <th>وضعیت</th>
                    <th>تاریخ ایجاد</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($projects as $project)
                    <tr>
                        <td>{{ $project->name }}</td>
                        <td>{{ $project->customer->full_name }}</td>
                        <td><a href="{{ $project->website_url }}" target="_blank" rel="noopener">{{ parse_url($project->website_url, PHP_URL_HOST) }}</a></td>
                        <td>{{ number_format($project->tickets_count) }}</td>
                        <td><x-status-badge :status="$project->status" /></td>
                        <td>{{ $project->created_at->format('Y/m/d') }}</td>
                        <td><a class="text-link" href="{{ route('admin.projects.edit', $project) }}">ویرایش</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $projects->links() }}</div>
    @endif
</section>
@endsection
