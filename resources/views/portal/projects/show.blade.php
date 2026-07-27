@extends('layouts.app')

@section('title', $project->name)
@section('heading', $project->name)
@section('description', 'جزئیات پروژه و تیکت‌های مرتبط')

@section('content')
<div class="page-actions">
    <div class="inline-meta">
        <x-status-badge :status="$project->status" />
        <a href="{{ $project->website_url }}" target="_blank" rel="noopener">{{ $project->website_url }}</a>
    </div>
    @if ($project->isActive())
        <a class="button button-primary" href="{{ route('portal.tickets.create', ['project_id' => $project->id]) }}">ثبت تیکت برای پروژه</a>
    @endif
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
                    <th>آخرین بروزرسانی</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($tickets as $ticket)
                    <tr>
                        <td><a href="{{ route('portal.tickets.show', $ticket) }}">{{ $ticket->ticket_number }}</a></td>
                        <td>{{ $ticket->subject }}</td>
                        <td><x-status-badge :status="$ticket->status" /></td>
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
