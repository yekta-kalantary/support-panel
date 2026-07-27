@extends('layouts.app')

@section('title', 'تیکت‌های من')
@section('heading', 'تیکت‌های من')
@section('description', 'پیگیری درخواست‌های پشتیبانی')

@section('content')
<div class="page-actions">
    <form method="get" class="filters">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="شماره یا عنوان تیکت">
        <select name="project_id">
            <option value="">همه پروژه‌ها</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" @selected((string) request('project_id') === (string) $project->id)>{{ $project->name }}</option>
            @endforeach
        </select>
        <select name="status">
            <option value="">همه وضعیت‌ها</option>
            @foreach (\App\Enums\TicketStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <button class="button button-secondary" type="submit">فیلتر</button>
        <a class="button button-ghost" href="{{ route('portal.tickets.index') }}">پاک کردن</a>
    </form>
    <a class="button button-primary" href="{{ route('portal.tickets.create') }}">ثبت تیکت</a>
</div>

<section class="card">
    @if ($tickets->isEmpty())
        <x-empty-state message="تیکتی پیدا نشد." />
    @else
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>شماره</th>
                    <th>عنوان</th>
                    <th>پروژه</th>
                    <th>وضعیت</th>
                    <th>آخرین پاسخ‌دهنده</th>
                    <th>آخرین بروزرسانی</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($tickets as $ticket)
                    <tr>
                        <td><a href="{{ route('portal.tickets.show', $ticket) }}">{{ $ticket->ticket_number }}</a></td>
                        <td>{{ $ticket->subject }}</td>
                        <td>{{ $ticket->project->name }}</td>
                        <td><x-status-badge :status="$ticket->status" /></td>
                        <td>{{ $ticket->latestMessage?->sender?->full_name ?? '—' }}</td>
                        <td>{{ $ticket->updated_at->format('Y/m/d H:i') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $tickets->links() }}</div>
    @endif
</section>
@endsection
