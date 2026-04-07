@extends('layouts.app')
@section('title', 'Manage Exams')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Manage Exams</h1>
    <a href="{{ route('admin.exam.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        + Create Exam
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Title</th>
                <th class="px-4 py-3 text-left">Duration</th>
                <th class="px-4 py-3 text-left">Questions</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($exams as $exam)
            <tr class="border-t">
                <td class="px-4 py-3 font-medium">{{ $exam->title }}</td>
                <td class="px-4 py-3">{{ $exam->duration_minutes }} mins</td>
                <td class="px-4 py-3">{{ $exam->questions_count }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded text-xs font-medium
                        {{ $exam->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $exam->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-4 py-3 flex gap-3">
                    <a href="{{ route('admin.exam.questions', $exam->id) }}" class="text-blue-600 hover:underline">Questions</a>
                    <form method="POST" action="{{ route('admin.exam.toggle', $exam->id) }}" class="inline">
                        @csrf
                        <button class="text-{{ $exam->is_active ? 'red' : 'green' }}-600 hover:underline">
                            {{ $exam->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.exam.delete', $exam->id) }}"
                          onsubmit="return confirm('Are you sure you want to delete this exam?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">No exams yet</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection