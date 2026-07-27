@extends('layouts.app')

@section('title', $ticket->ticket_number)
@section('heading', $ticket->subject)
@section('description', $ticket->ticket_number)

@section('content')
<div class="ticket-layout">
    <div class="ticket-main">
        <section class="card">
            <div class="card-header">
                <h2>گفت‌وگو</h2>
                <x-status-badge :status="$ticket->status" />
            </div>
            @include('tickets._conversation')
        </section>

        @include('tickets._reply-form', [
            'canReply' => true,
            'replyAction' => route('admin.tickets.reply', $ticket),
        ])
    </div>

    <aside class="ticket-sidebar">
        <section class="card">
            <div class="card-header"><h2>اطلاعات تیکت</h2></div>
            <dl class="details">
                <div><dt>شماره تیکت</dt><dd>{{ $ticket->ticket_number }}</dd></div>
                <div><dt>مشتری</dt><dd>{{ $ticket->customer->full_name }}</dd></div>
                <div><dt>پروژه</dt><dd>{{ $ticket->project->name }}</dd></div>
                <div><dt>وب‌سایت</dt><dd><a href="{{ $ticket->project->website_url }}" target="_blank" rel="noopener">{{ parse_url($ticket->project->website_url, PHP_URL_HOST) }}</a></dd></div>
                <div><dt>ثبت‌شده در</dt><dd>{{ $ticket->created_at->format('Y/m/d H:i') }}</dd></div>
                <div><dt>آخرین بروزرسانی</dt><dd>{{ $ticket->updated_at->format('Y/m/d H:i') }}</dd></div>
            </dl>
        </section>

        <section class="card">
            <div class="card-header"><h2>تغییر وضعیت</h2></div>
            <form method="post" action="{{ route('admin.tickets.status.update', $ticket) }}" class="form-stack">
                @csrf
                @method('PATCH')
                <label>
                    <span>وضعیت</span>
                    <select name="status" required>
                        @foreach (\App\Enums\TicketStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected($ticket->status === $status)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </label>
                <button class="button button-primary button-block" type="submit">ذخیره وضعیت</button>
            </form>
        </section>
    </aside>
</div>
@endsection
