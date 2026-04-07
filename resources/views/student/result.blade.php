@extends('layouts.app')
@section('title', 'Result')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-8 mb-6 text-center">
        <h1 class="text-2xl font-bold mb-2">{{ $session->exam->title }}</h1>
        <div class="text-6xl font-bold my-6
            {{ $session->score >= ($session->exam->total_marks * 0.5) ? 'text-green-500' : 'text-red-500' }}">
            {{ $session->score }}<span class="text-2xl text-gray-400">/{{ $session->exam->total_marks }}</span>
        </div>
        <p class="text-gray-500">
            {{ $session->score >= ($session->exam->total_marks * 0.5) ? '✅ Passed' : '❌ Failed' }}
        </p>
        @if($session->violations->count() > 0)
            <p class="text-red-500 mt-2">⚠ {{ $session->violations->count() }} violation(s) recorded</p>
        @endif
        <p class="text-gray-400 text-sm mt-2">Submitted: {{ $session->submitted_at->format('d M Y, h:i A') }}</p>
    </div>

    <div class="space-y-4">
        @foreach($session->exam->questions as $index => $question)
            @php
                $answer = $session->answers->firstWhere('question_id', $question->id);
                $isCorrect = $answer && $answer->option && $answer->option->is_correct;
            @endphp
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between mb-3">
                    <span class="font-medium">Q{{ $index + 1 }}. {{ $question->question_text }}</span>
                    <span class="{{ $isCorrect ? 'text-green-500' : 'text-red-500' }}">
                        {{ $isCorrect ? '✓' : '✗' }}
                    </span>
                </div>
                @foreach($question->options as $option)
                    <div class="px-3 py-2 rounded mb-1 text-sm
                        {{ $option->is_correct ? 'bg-green-100 text-green-800' : '' }}
                        {{ $answer && $answer->option_id == $option->id && !$option->is_correct ? 'bg-red-100 text-red-800' : '' }}">
                        {{ $option->option_text }}
                        @if($option->is_correct) ✓ @endif
                        @if($answer && $answer->option_id == $option->id && !$option->is_correct) ✗ (your answer) @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <div class="mt-6 text-center">
        @if(Auth::user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Back to Dashboard
            </a>
        @else
            <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Back to Dashboard
            </a>
        @endif
    </div>
</div>
@endsection
