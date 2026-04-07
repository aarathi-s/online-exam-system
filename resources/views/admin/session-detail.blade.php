@extends('layouts.app')
@section('title', 'Session Detail')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h1 class="text-xl font-bold mb-4">Session Detail</h1>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">Student:</span> <strong>{{ $session->user->name }}</strong></div>
            <div><span class="text-gray-500">Email:</span> {{ $session->user->email }}</div>
            <div><span class="text-gray-500">Exam:</span> {{ $session->exam->title }}</div>
            <div><span class="text-gray-500">Score:</span> <strong>{{ $session->score }}/{{ $session->exam->total_marks }}</strong></div>
            <div><span class="text-gray-500">Started:</span> {{ $session->started_at->format('d M Y, h:i A') }}</div>
            <div><span class="text-gray-500">Submitted:</span> {{ $session->submitted_at->format('d M Y, h:i A') }}</div>
        </div>
    </div>

    @if($session->violations->isNotEmpty())
    <div class="bg-red-50 rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-red-700 mb-3">⚠ Violations ({{ $session->violations->count() }})</h2>
        <div class="space-y-2">
            @foreach($session->violations as $violation)
            <div class="flex justify-between text-sm">
                <span class="text-red-600">{{ $violation->type }}</span>
                <span class="text-gray-500">{{ $violation->occurred_at->format('h:i:s A') }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="space-y-4">
        @foreach($session->exam->questions as $index => $question)
            @php
                $answer = $session->answers->firstWhere('question_id', $question->id);
                $isCorrect = $answer && $answer->option && $answer->option->is_correct;
            @endphp
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex justify-between mb-2">
                    <span class="font-medium">Q{{ $index + 1 }}. {{ $question->question_text }}</span>
                    <span class="{{ $isCorrect ? 'text-green-500' : 'text-red-500' }}">{{ $isCorrect ? '✓' : '✗' }}</span>
                </div>
                @foreach($question->options as $option)
                <div class="text-sm px-3 py-1 rounded mb-1
                    {{ $option->is_correct ? 'bg-green-100 text-green-700' : '' }}
                    {{ $answer && $answer->option_id == $option->id && !$option->is_correct ? 'bg-red-100 text-red-700' : '' }}">
                    {{ $option->option_text }}
                </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.results') }}" class="text-blue-600 hover:underline">← Back to Results</a>
    </div>
</div>
@endsection
