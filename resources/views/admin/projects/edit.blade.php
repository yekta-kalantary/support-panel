@extends('layouts.app')

@section('title', 'ویرایش پروژه')
@section('heading', 'ویرایش پروژه')
@section('description', $project->name)

@section('content')
<section class="card">
    <form method="post" action="{{ route('admin.projects.update', $project) }}" class="form-stack">
        @csrf
        @method('PUT')
        @include('admin.projects._form', ['submitLabel' => 'ذخیره تغییرات'])
    </form>
</section>
@endsection
