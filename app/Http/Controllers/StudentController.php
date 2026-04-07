<?php
namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard() {
        $exams = Exam::where('is_active', true)->get();
        $mySessions = ExamSession::with('exam')
            ->where('user_id', Auth::id())
            ->where('status', 'submitted')
            ->latest()->get();
        return view('student.dashboard', compact('exams', 'mySessions'));
    }

    public function result($sessionId) {
        $session = ExamSession::with(['exam.questions.options', 'answers.option', 'violations'])
            ->findOrFail($sessionId);
        if ($session->user_id !== Auth::id() && !Auth::user()->isAdmin()) abort(403);
        return view('student.result', compact('session'));
    }
}