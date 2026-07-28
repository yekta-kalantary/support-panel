@extends('layouts.app')

@section('title', $project->name)
@section('heading', $project->name)
@section('description', 'اطلاعات پروژه و تیکت‌های مرتبط')

@section('content')
<div class="page-actions">
    <a class="button button-ghost" href="{{ route('portal.projects.index') }}">بازگشت به پروژه‌ها</a>
    @if ($project->isActive())
        <a class="button button-primary" href="{{ route('portal.tickets.create', ['project_id' => $project->id]) }}">ثبت تیکت برای پروژه</a>
    @endif
</div>

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
                        <td><a class="text-link" href="{{ route('portal.tickets.show', $ticket) }}">مشاهده تیکت</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $tickets->links() }}</div>
    @endif
</section>
@endsection
