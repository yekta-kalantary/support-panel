@extends('layouts.app')

@section('title', 'داشبورد مدیریت')
@section('heading', 'داشبورد مدیریت')
@section('description', 'نمای کلی مشتریان، پروژه‌ها و تیکت‌ها')

@section('content')
<div class="stats-grid">
    <div class="stat-card"><span>کل مشتریان</span><strong>{{ number_format($stats['customers']) }}</strong></div>
    <div class="stat-card"><span>مشتریان فعال</span><strong>{{ number_format($stats['activeCustomers']) }}</strong></div>
    <div class="stat-card"><span>مشتریان غیرفعال</span><strong>{{ number_format($stats['inactiveCustomers']) }}</strong></div>
    <div class="stat-card"><span>کل پروژه‌ها</span><strong>{{ number_format($stats['projects']) }}</strong></div>
    <div class="stat-card"><span>پروژه‌های فعال</span><strong>{{ number_format($stats['activeProjects']) }}</strong></div>
    <div class="stat-card"><span>تیکت‌های باز</span><strong>{{ number_format($stats['openTickets']) }}</strong></div>
    <div class="stat-card"><span>درحال بررسی</span><strong>{{ number_format($stats['inProgressTickets']) }}</strong></div>
    <div class="stat-card"><span>تیکت‌های بسته</span><strong>{{ number_format($stats['closedTickets']) }}</strong></div>
</div>

<section class="card">
    <div class="card-header">
        <h2>آخرین تیکت‌های بروزرسانی‌شده</h2>
        <a class="button button-secondary" href="{{ route('admin.tickets.index') }}">مشاهده همه</a>
    </div>

    @if ($recentTickets->isEmpty())
        <x-empty-state message="هنوز تیکتی ثبت نشده است." />
    @else
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>شماره</th>
                    <th>عنوان</th>
                    <th>مشتری</th>
                    <th>پروژه</th>
                    <th>وضعیت</th>
                    <th>آخرین بروزرسانی</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($recentTickets as $ticket)
                    <tr>
                        <td><a href="{{ route('admin.tickets.show', $ticket) }}">{{ $ticket->ticket_number }}</a></td>
                        <td>{{ $ticket->subject }}</td>
                        <td>{{ $ticket->customer->full_name }}</td>
                        <td>{{ $ticket->project->name }}</td>
                        <td><x-status-badge :status="$ticket->status" /></td>
                        <td>{{ $ticket->updated_at->format('Y/m/d H:i') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
@endsection
