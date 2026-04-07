@extends('layouts.app')
@section('title', 'All Results')
@section('content')
<h1 class="text-2xl font-bold mb-6">All Results</h1>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Student</th>
                <th class="px-4 py-3 text-left">Exam</th>
                <th class="px-4 py-3 text-left">Score</th>
                <th class="px-4 py-3 text-left">Result</th>
                <th class="px-4 py-3 text-left">Violations</th>
                <th class="px-4 py-3 text-left">Submitted</th>
                <th class="px-4 py-3 text-left">Detail</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $session)
            <tr class="border-t">
                <td class="px-4 py-3">{{ $session->user->name }}</td>
                <td class="px-4 py-3">{{ $session->exam->title }}</td>
                <td class="px-4 py-3 font-semibold">{{ $session->score }}/{{ $session->exam->total_marks }}</td>
                <td class="px-4 py-3">
                    @php $passed = $session->score >= ($session->exam->total_marks * 0.5) @endphp
                    <span class="px-2 py-1 rounded text-xs font-medium
                        {{ $passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $passed ? 'Pass' : 'Fail' }}
                    </span>
                </td>
                <td class="px-4 py-3">{{ $session->violations->count() }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $session->submitted_at->format('d M Y, h:i A') }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('admin.session.detail', $session->id) }}" class="text-blue-600 hover:underline">View</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">No results yet</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection