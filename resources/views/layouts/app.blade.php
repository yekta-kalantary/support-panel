<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'داشبورد') | {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @vite('resources/css/fonts.css')
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <a class="brand sidebar-brand" href="{{ route('home') }}">
            <span class="brand-mark">پ</span>
            <div>
                <strong>{{ config('app.name') }}</strong>
                <small>{{ auth()->user()->role->label() }}</small>
            </div>
        </a>

        <nav class="nav">
            @if (auth()->user()->isAdmin())
                <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">داشبورد</a>
                <a class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">مشتریان</a>
                <a class="{{ request()->routeIs('admin.projects.*') ? 'active' : '' }}" href="{{ route('admin.projects.index') }}">پروژه‌ها</a>
                <a class="{{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}" href="{{ route('admin.tickets.index') }}">تیکت‌ها</a>
            @else
                <a class="{{ request()->routeIs('portal.dashboard') ? 'active' : '' }}" href="{{ route('portal.dashboard') }}">داشبورد</a>
                <a class="{{ request()->routeIs('portal.projects.*') ? 'active' : '' }}" href="{{ route('portal.projects.index') }}">پروژه‌های من</a>
                <a class="{{ request()->routeIs('portal.tickets.index', 'portal.tickets.show') ? 'active' : '' }}" href="{{ route('portal.tickets.index') }}">تیکت‌های من</a>
                <a class="{{ request()->routeIs('portal.tickets.create') ? 'active' : '' }}" href="{{ route('portal.tickets.create') }}">ثبت تیکت</a>
            @endif

            <a class="{{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">پروفایل</a>
        </nav>

        <form action="{{ route('logout') }}" method="post" class="sidebar-footer">
            @csrf
            <button class="button button-ghost button-block" type="submit">خروج از حساب</button>
        </form>
    </aside>

    <main class="main">
        <header class="topbar">
            <div>
                <h1>@yield('heading', 'داشبورد')</h1>
                @hasSection('description')
                    <p>@yield('description')</p>
                @endif
            </div>
            <div class="user-chip">
                <span>{{ auth()->user()->full_name }}</span>
                <small>{{ auth()->user()->email }}</small>
            </div>
        </header>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>اطلاعات فرم نیاز به اصلاح دارد:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script>
document.querySelectorAll('[data-confirm]').forEach((element) => {
    element.addEventListener('click', (event) => {
        if (! window.confirm(element.dataset.confirm)) {
            event.preventDefault();
        }
    });
});
</script>
</body>
</html>
