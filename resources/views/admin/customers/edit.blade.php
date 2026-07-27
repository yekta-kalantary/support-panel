@extends('layouts.app')

@section('title', 'ویرایش مشتری')
@section('heading', 'ویرایش مشتری')
@section('description', $customer->full_name)

@section('content')
<section class="card">
    <form method="post" action="{{ route('admin.customers.update', $customer) }}" class="form-stack">
        @csrf
        @method('PUT')
        @include('admin.customers._form', ['submitLabel' => 'ذخیره تغییرات'])
    </form>
</section>
@endsection
