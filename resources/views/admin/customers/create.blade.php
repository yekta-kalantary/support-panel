@extends('layouts.app')

@section('title', 'مشتری جدید')
@section('heading', 'ایجاد مشتری')
@section('description', 'ثبت حساب جدید برای مشتری')

@section('content')
<section class="card">
    <form method="post" action="{{ route('admin.customers.store') }}" class="form-stack">
        @csrf
        @include('admin.customers._form', ['submitLabel' => 'ایجاد مشتری'])
    </form>
</section>
@endsection
