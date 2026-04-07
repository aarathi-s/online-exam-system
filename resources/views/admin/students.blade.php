@extends('layouts.app')
@section('title', 'Manage Students')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Manage Students</h1>
    <a href="{{ route('admin.student.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        + Add Student
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-left">Joined</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
            <tr class="border-t">
                <td class="px-4 py-3 font-medium">{{ $student->name }}</td>
                <td class="px-4 py-3">{{ $student->email }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $student->created_at->format('d M Y') }}</td>
                <td class="px-4 py-3">
                    <form method="POST" action="{{ route('admin.student.delete', $student->id) }}"
                          onsubmit="return confirm('Are you sure you want to delete this student?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">No students yet</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection