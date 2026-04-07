@extends('layouts.app')
@section('title', 'Exam Questions')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ $exam->title }}</h1>
            <p class="text-gray-500 text-sm">{{ $exam->questions->count() }} questions added</p>
        </div>
        <a href="{{ route('admin.exams') }}" class="text-blue-600 hover:underline text-sm">← Back to Exams</a>
    </div>

    <!-- Add Question Form -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Add New Question</h2>
        <form method="POST" action="{{ route('admin.exam.question.store', $exam->id) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Question</label>
                <textarea name="question_text" rows="2" required
                          class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Enter your question here">{{ old('question_text') }}</textarea>
            </div>

            <div class="mb-4 space-y-2">
                <label class="block text-sm font-medium text-gray-700">Options (select the correct one)</label>
                @for($i = 0; $i < 4; $i++)
                <div class="flex items-center gap-3">
                    <input type="radio" name="correct_option" value="{{ $i }}" {{ old('correct_option') == $i ? 'checked' : '' }} required>
                    <input type="text" name="options[]" value="{{ old('options.'.$i) }}"
                           class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Option {{ $i + 1 }}" required>
                </div>
                @endfor
            </div>

            @error('correct_option')<p class="text-red-500 text-xs mb-2">Please select the correct answer.</p>@enderror

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Add Question
            </button>
        </form>
    </div>

    <!-- Existing Questions -->
    @if($exam->questions->isNotEmpty())
    <h2 class="text-lg font-semibold mb-3">Added Questions</h2>
    <div class="space-y-3">
        @foreach($exam->questions as $index => $question)
        <div class="bg-white rounded-lg shadow p-4">
            <p class="font-medium mb-2">Q{{ $index + 1 }}. {{ $question->question_text }}</p>
            <div class="space-y-1">
                @foreach($question->options as $option)
                <div class="text-sm px-3 py-1 rounded
                    {{ $option->is_correct ? 'bg-green-100 text-green-700 font-medium' : 'text-gray-600' }}">
                    {{ $option->option_text }} {{ $option->is_correct ? '✓' : '' }}
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection