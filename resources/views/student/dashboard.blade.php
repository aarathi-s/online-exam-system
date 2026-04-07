@extends('layouts.app')
@section('title', 'Student Dashboard')
@section('content')
<h1 class="text-2xl font-bold mb-6">Available Exams</h1>

@if($exams->isEmpty())
    <div class="bg-white rounded-lg p-8 text-center text-gray-500">No exams available right now.</div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        @foreach($exams as $exam)
            @php
                $taken = $mySessions->firstWhere('exam_id', $exam->id);
            @endphp
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-2">{{ $exam->title }}</h2>
                <p class="text-gray-500 text-sm mb-3">{{ $exam->description }}</p>
                <div class="text-sm text-gray-600 mb-4">
                    <span>⏱ {{ $exam->duration_minutes }} mins</span>
                    <span class="ml-3">📝 {{ $exam->total_marks }} marks</span>
                </div>
                @if($taken)
                    <a href="{{ route('student.result', $taken->id) }}"
                       class="block text-center bg-green-500 text-white py-2 rounded hover:bg-green-600">
                        View Result ({{ $taken->score }}/{{ $exam->total_marks }})
                    </a>
                @else
                    <a href="{{ route('exam.start', $exam->id) }}"
                       class="block text-center bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                        Start Exam
                    </a>
                @endif
            </div>
        @endforeach
    </div>
@endif

@if($mySessions->isNotEmpty())
    <h2 class="text-xl font-bold mb-4">My Results</h2>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Exam</th>
                    <th class="px-4 py-3 text-left">Score</th>
                    <th class="px-4 py-3 text-left">Submitted</th>
                    <th class="px-4 py-3 text-left">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mySessions as $session)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ $session->exam->title }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $session->score }}/{{ $session->exam->total_marks }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $session->submitted_at->format('d M Y, h:i A') }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('student.result', $session->id) }}" class="text-blue-600 hover:underline">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
