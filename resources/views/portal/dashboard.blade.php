@extends('layouts.app')

@section('title', 'داشبورد')
@section('heading', 'داشبورد')
@section('description', 'خلاصه پروژه‌ها و درخواست‌های پشتیبانی شما')

@section('content')
<div class="stats-grid">
    <div class="stat-card"><span>پروژه‌های فعال</span><strong>{{ number_format($stats['activeProjects']) }}</strong></div>
    <div class="stat-card"><span>کل تیکت‌ها</span><strong>{{ number_format($stats['tickets']) }}</strong></div>
    <div class="stat-card"><span>تیکت‌های باز</span><strong>{{ number_format($stats['openTickets']) }}</strong></div>
    <div class="stat-card"><span>درحال بررسی</span><strong>{{ number_format($stats['inProgressTickets']) }}</strong></div>
    <div class="stat-card"><span>تیکت‌های بسته</span><strong>{{ number_format($stats['closedTickets']) }}</strong></div>
</div>

<div class="page-actions">
    <div></div>
    <a class="button button-primary" href="{{ route('portal.tickets.create') }}">ثبت تیکت جدید</a>
</div>

<section class="card">
    <div class="card-header">
        <h2>آخرین تیکت‌ها</h2>
        <a class="button button-secondary" href="{{ route('portal.tickets.index') }}">مشاهده همه</a>
    </div>

    @if ($recentTickets->isEmpty())
        <x-empty-state message="هنوز تیکتی ثبت نکرده‌اید." />
    @else
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>شماره</th>
                    <th>عنوان</th>
                    <th>پروژه</th>
                    <th>وضعیت</th>
                    <th>آخرین بروزرسانی</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($recentTickets as $ticket)
                    <tr>
                        <td><a href="{{ route('portal.tickets.show', $ticket) }}">{{ $ticket->ticket_number }}</a></td>
                        <td>{{ $ticket->subject }}</td>
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
