<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Home')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<nav class="bg-white shadow mb-6">
    <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}"
           class="text-xl font-bold text-blue-600">ExamSystem</a>
        <div class="flex items-center gap-4">
            <span class="text-gray-600">{{ auth()->user()->name }}</span>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-blue-600 hover:underline">Dashboard</a>
                <a href="{{ route('admin.exams') }}" class="text-sm text-blue-600 hover:underline">Exams</a>
                <a href="{{ route('admin.students') }}" class="text-sm text-blue-600 hover:underline">Students</a>
                <a href="{{ route('admin.results') }}" class="text-sm text-blue-600 hover:underline">Results</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-sm text-red-500 hover:underline">Logout</button>
            </form>
        </div>
    </div>
</nav>

<div class="max-w-6xl mx-auto px-4">
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="bg-blue-100 text-blue-800 px-4 py-3 rounded mb-4">{{ session('info') }}</div>
    @endif

    @yield('content')
</div>

@yield('scripts')
</body>
</html>