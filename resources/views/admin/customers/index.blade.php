@extends('layouts.app')

@section('title', 'مشتریان')
@section('heading', 'مشتریان')
@section('description', 'مدیریت حساب‌های مشتریان')

@section('content')
<div class="page-actions">
    <form method="get" class="filters">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="نام، ایمیل یا موبایل">
        <select name="status">
            <option value="">همه وضعیت‌ها</option>
            @foreach (\App\Enums\RecordStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <button class="button button-secondary" type="submit">فیلتر</button>
        <a class="button button-ghost" href="{{ route('admin.customers.index') }}">پاک کردن</a>
    </form>
    <a class="button button-primary" href="{{ route('admin.customers.create') }}">مشتری جدید</a>
</div>

<section class="card">
    @if ($customers->isEmpty())
        <x-empty-state message="مشتری‌ای پیدا نشد." />
    @else
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>نام</th>
                    <th>ایمیل</th>
                    <th>شماره موبایل</th>
                    <th>پروژه‌ها</th>
                    <th>وضعیت</th>
                    <th>تاریخ ایجاد</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($customers as $customer)
                    <tr>
                        <td>{{ $customer->full_name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->mobile }}</td>
                        <td>{{ number_format($customer->projects_count) }}</td>
                        <td><x-status-badge :status="$customer->status" /></td>
                        <td>{{ $customer->created_at->format('Y/m/d') }}</td>
                        <td><a class="text-link" href="{{ route('admin.customers.edit', $customer) }}">ویرایش</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $customers->links() }}</div>
    @endif
</section>
@endsection
