<?php
namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Answer;
use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function start($examId) {
        $exam = Exam::with('questions.options')->findOrFail($examId);

        if (!$exam->is_active) {
            return back()->with('error', 'This exam is not active.');
        }

        $existing = ExamSession::where('user_id', Auth::id())
            ->where('exam_id', $examId)
            ->where('status', 'submitted')
            ->first();

        if ($existing) {
            return redirect()->route('student.result', $existing->id)
                ->with('info', 'You have already completed this exam.');
        }

        $session = ExamSession::firstOrCreate(
            ['user_id' => Auth::id(), 'exam_id' => $examId, 'status' => 'in_progress'],
            ['started_at' => now()]
        );

        $questions = $exam->questions->shuffle();
        $firstQuestion = $questions->first();

        return redirect()->route('exam.question', ['sessionId' => $session->id, 'questionId' => $firstQuestion->id]);
    }

    public function showQuestion($sessionId, $questionId) {
    $session = ExamSession::with('exam.questions.options')->findOrFail($sessionId);

    if ($session->user_id !== Auth::id()) abort(403);
    if ($session->status === 'submitted') {
        return redirect()->route('student.result', $session->id);
    }

    $exam = $session->exam;
    $questions = $exam->questions;
    $question = $questions->find($questionId);

    if (!$question) abort(404);

    $currentAnswer = Answer::where('exam_session_id', $sessionId)
        ->where('question_id', $questionId)->first();

    // collect all answered question IDs for progress bar
    $answeredIds = Answer::where('exam_session_id', $sessionId)
        ->pluck('question_id');

    $questionIndex = $questions->search(fn($q) => $q->id == $questionId);
    $elapsed = now()->diffInSeconds($session->started_at);
    $remaining = ($exam->duration_minutes * 60) - $elapsed;

    if ($remaining <= 0) {
        return $this->autoSubmit($session);
    }

    $answeredCount = $answeredIds->count();

    return view('exam.question', compact(
        'session', 'exam', 'question', 'questions',
        'questionIndex', 'currentAnswer', 'remaining', 'answeredIds', 'answeredCount'
    ));
}

    public function saveAnswer(Request $request, $sessionId, $questionId) {
        $session = ExamSession::findOrFail($sessionId);
        if ($session->user_id !== Auth::id()) abort(403);
        if ($session->status === 'submitted') return response()->json(['status' => 'already_submitted']);

        Answer::updateOrCreate(
            ['exam_session_id' => $sessionId, 'question_id' => $questionId],
            ['option_id' => $request->option_id]
        );

        return response()->json(['status' => 'saved']);
    }

    public function submit(Request $request, $sessionId) {
        $session = ExamSession::with('exam.questions.options', 'answers')->findOrFail($sessionId);
        if ($session->user_id !== Auth::id()) abort(403);
        if ($session->status === 'submitted') {
            return redirect()->route('student.result', $session->id);
        }
        return $this->autoSubmit($session);
    }

    public function autoSubmit($session) {
        if ($session->status === 'submitted') {
            return redirect()->route('student.result', $session->id);
        }

        $session->load('exam.questions.options', 'answers');
        $score = 0;

        foreach ($session->exam->questions as $question) {
            $answer = $session->answers->where('question_id', $question->id)->first();
            if ($answer && $answer->option) {
                if ($answer->option->is_correct) $score++;
            }
        }

        $session->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'score' => $score,
        ]);

        return redirect()->route('student.result', $session->id);
    }

    public function recordViolation(Request $request, $sessionId) {
        $session = ExamSession::findOrFail($sessionId);
        if ($session->user_id !== Auth::id()) abort(403);

        Violation::create([
            'exam_session_id' => $sessionId,
            'type' => $request->type,
            'occurred_at' => now(),
        ]);

        $violationCount = Violation::where('exam_session_id', $sessionId)->count();

        if ($violationCount >= 3) {
            $this->autoSubmit($session);
            return response()->json(['status' => 'terminated', 'message' => 'Exam terminated due to violations.']);
        }

        return response()->json(['status' => 'recorded', 'violations' => $violationCount]);
    }
}