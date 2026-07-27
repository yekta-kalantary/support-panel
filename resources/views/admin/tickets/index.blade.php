@extends('layouts.app')

@section('title', 'تیکت‌ها')
@section('heading', 'تیکت‌ها')
@section('description', 'مشاهده و رسیدگی به درخواست‌های مشتریان')

@section('content')
<div class="page-actions">
    <form method="get" class="filters filters-wide">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="شماره، عنوان، مشتری یا پروژه">
        <select name="customer_id">
            <option value="">همه مشتریان</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" @selected((string) request('customer_id') === (string) $customer->id)>{{ $customer->full_name }}</option>
            @endforeach
        </select>
        <select name="status">
            <option value="">همه وضعیت‌ها</option>
            @foreach (\App\Enums\TicketStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <button class="button button-secondary" type="submit">فیلتر</button>
        <a class="button button-ghost" href="{{ route('admin.tickets.index') }}">پاک کردن</a>
    </form>
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
                    <th>مشتری</th>
                    <th>پروژه</th>
                    <th>وضعیت</th>
                    <th>آخرین پاسخ‌دهنده</th>
                    <th>آخرین بروزرسانی</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($tickets as $ticket)
                    <tr>
                        <td><a href="{{ route('admin.tickets.show', $ticket) }}">{{ $ticket->ticket_number }}</a></td>
                        <td>{{ $ticket->subject }}</td>
                        <td>{{ $ticket->customer->full_name }}</td>
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
