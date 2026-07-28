@extends('layouts.app')

@section('title', $project->name)
@section('heading', $project->name)
@section('description', 'اطلاعات پروژه، مشتری و تیکت‌های مرتبط')

@section('content')
<div class="page-actions">
    <a class="button button-ghost" href="{{ route('admin.projects.index') }}">بازگشت به پروژه‌ها</a>
    <a class="button button-primary" href="{{ route('admin.projects.edit', $project) }}">ویرایش پروژه</a>
</div>

<div class="grid grid-2">
    <section class="card">
        <div class="card-header">
            <h2>اطلاعات پروژه</h2>
            <x-status-badge :status="$project->status" />
        </div>

        <dl class="details">
            <div>
                <dt>نام پروژه</dt>
                <dd>{{ $project->name }}</dd>
            </div>
            <div>
                <dt>آدرس وب‌سایت</dt>
                <dd><a href="{{ $project->website_url }}" target="_blank" rel="noopener">{{ $project->website_url }}</a></dd>
            </div>
            <div>
                <dt>تعداد تیکت‌ها</dt>
                <dd>{{ number_format($project->tickets_count) }}</dd>
            </div>
            <div>
                <dt>تاریخ ایجاد</dt>
                <dd>{{ $project->created_at->format('Y/m/d H:i') }}</dd>
            </div>
            <div>
                <dt>آخرین بروزرسانی</dt>
                <dd>{{ $project->updated_at->format('Y/m/d H:i') }}</dd>
            </div>
        </dl>
    </section>

    <section class="card">
        <div class="card-header">
            <h2>اطلاعات مشتری</h2>
            <x-status-badge :status="$project->customer->status" />
        </div>

        <dl class="details">
            <div>
                <dt>نام و نام خانوادگی</dt>
                <dd>{{ $project->customer->full_name }}</dd>
            </div>
            <div>
                <dt>ایمیل</dt>
                <dd>{{ $project->customer->email }}</dd>
            </div>
            <div>
                <dt>شماره موبایل</dt>
                <dd>{{ $project->customer->mobile }}</dd>
            </div>
        </dl>
    </section>
</div>

<section class="card">
    <div class="card-header">
        <h2>تیکت‌های پروژه</h2>
        <span class="muted">{{ number_format($project->tickets_count) }} تیکت</span>
    </div>

    @if ($tickets->isEmpty())
        <x-empty-state message="برای این پروژه هنوز تیکتی ثبت نشده است." />
    @else
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>شماره</th>
                    <th>عنوان</th>
                    <th>وضعیت</th>
                    <th>تاریخ ایجاد</th>
                    <th>آخرین بروزرسانی</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->ticket_number }}</td>
                        <td>{{ $ticket->subject }}</td>
                        <td><x-status-badge :status="$ticket->status" /></td>
                        <td>{{ $ticket->created_at->format('Y/m/d H:i') }}</td>
                        <td>{{ $ticket->updated_at->format('Y/m/d H:i') }}</td>
                        <td><a class="text-link" href="{{ route('admin.tickets.show', $ticket) }}">مشاهده تیکت</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $tickets->links() }}</div>
    @endif
</section>
@endsection
