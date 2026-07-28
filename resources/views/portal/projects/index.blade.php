@extends('layouts.app')

@section('title', 'پروژه‌های من')
@section('heading', 'پروژه‌های من')
@section('description', 'فهرست پروژه‌های متصل به حساب شما')

@section('content')
<section class="card">
    @if ($projects->isEmpty())
        <x-empty-state message="پروژه‌ای برای حساب شما تعریف نشده است." />
    @else
        <div class="project-grid">
            @foreach ($projects as $project)
                <article class="project-card">
                    <div class="project-card-header">
                        <h2><a href="{{ route('portal.projects.show', $project) }}">{{ $project->name }}</a></h2>
                        <x-status-badge :status="$project->status" />
                    </div>
                    <a href="{{ $project->website_url }}" target="_blank" rel="noopener">{{ $project->website_url }}</a>
                    <div class="project-meta">
                        <span>{{ number_format($project->tickets_count) }} تیکت</span>
                        <span>ایجاد: {{ $project->created_at->format('Y/m/d') }}</span>
                    </div>
                    <div class="project-actions">
                        <a class="button button-secondary" href="{{ route('portal.projects.show', $project) }}">مشاهده پروژه</a>
                        @if ($project->isActive())
                            <a class="button button-primary" href="{{ route('portal.tickets.create', ['project_id' => $project->id]) }}">ثبت تیکت</a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
        <div class="pagination">{{ $projects->links() }}</div>
    @endif
</section>
@endsection
