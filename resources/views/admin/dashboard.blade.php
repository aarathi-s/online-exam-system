@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<h1 class="text-2xl font-bold mb-6">Admin Dashboard</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <div class="text-4xl font-bold text-blue-600">{{ $totalExams }}</div>
        <div class="text-gray-500 mt-1">Total Exams</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <div class="text-4xl font-bold text-green-600">{{ $totalStudents }}</div>
        <div class="text-gray-500 mt-1">Total Students</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <div class="text-4xl font-bold text-purple-600">{{ $totalSessions }}</div>
        <div class="text-gray-500 mt-1">Completed Exams</div>
    </div>
</div>

<div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-bold">Recent Submissions</h2>
    <div class="flex gap-2">
        <a href="{{ route('admin.student.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            + Add Student
        </a>
        <a href="{{ route('admin.exam.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Create Exam
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Student</th>
                <th class="px-4 py-3 text-left">Exam</th>
                <th class="px-4 py-3 text-left">Score</th>
                <th class="px-4 py-3 text-left">Submitted</th>
                <th class="px-4 py-3 text-left">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentSessions as $session)
            <tr class="border-t">
                <td class="px-4 py-3">{{ $session->user->name }}</td>
                <td class="px-4 py-3">{{ $session->exam->title }}</td>
                <td class="px-4 py-3 font-semibold">{{ $session->score }}/{{ $session->exam->total_marks }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $session->submitted_at->format('d M Y, h:i A') }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('admin.session.detail', $session->id) }}" class="text-blue-600 hover:underline">View</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">No submissions yet</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection