@extends('layouts.app')

@section('title', 'پروژه جدید')
@section('heading', 'ایجاد پروژه')
@section('description', 'اتصال پروژه جدید به یکی از مشتریان')

@section('content')
<section class="card">
    <form method="post" action="{{ route('admin.projects.store') }}" class="form-stack">
        @csrf
        @include('admin.projects._form', ['submitLabel' => 'ایجاد پروژه'])
    </form>
</section>
@endsection
