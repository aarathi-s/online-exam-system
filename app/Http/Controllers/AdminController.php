<?php
namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Question;
use App\Models\Option;
use App\Models\ExamSession;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard() {
        $totalExams = Exam::count();
        $totalStudents = User::where('is_admin', false)->count();
        $totalSessions = ExamSession::where('status', 'submitted')->count();
        $recentSessions = ExamSession::with(['user', 'exam'])
            ->where('status', 'submitted')
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->take(10)
            ->get();
        return view('admin.dashboard', compact('totalExams', 'totalStudents', 'totalSessions', 'recentSessions'));
    }

    public function exams() {
        $exams = Exam::withCount('questions')->latest()->get();
        return view('admin.exams', compact('exams'));
    }

    public function createExam() {
        return view('admin.create-exam');
    }

    public function storeExam(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        $exam = Exam::create([
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'total_marks' => 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.exam.questions', $exam->id)
            ->with('success', 'Exam created! Now add questions.');
    }

    public function examQuestions($examId) {
        $exam = Exam::with('questions.options')->findOrFail($examId);
        return view('admin.exam-questions', compact('exam'));
    }

    public function storeQuestion(Request $request, $examId) {
        $request->validate([
            'question_text' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_option' => 'required|integer',
        ]);

        $exam = Exam::findOrFail($examId);

        $question = Question::create([
            'exam_id' => $examId,
            'question_text' => $request->question_text,
            'marks' => 1,
        ]);

        foreach ($request->options as $index => $optionText) {
            Option::create([
                'question_id' => $question->id,
                'option_text' => $optionText,
                'is_correct' => ($index == $request->correct_option),
            ]);
        }

        $exam->update(['total_marks' => $exam->questions()->count()]);

        return back()->with('success', 'Question added!');
    }

    public function toggleExam($examId) {
        $exam = Exam::findOrFail($examId);
        $exam->update(['is_active' => !$exam->is_active]);
        return back()->with('success', 'Exam status updated!');
    }

    public function results() {
        $sessions = ExamSession::with(['user', 'exam'])
            ->where('status', 'submitted')
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->get();
        return view('admin.results', compact('sessions'));
    }

    public function sessionDetail($sessionId) {
        $session = ExamSession::with(['user', 'exam', 'answers.question', 'answers.option', 'violations'])
            ->findOrFail($sessionId);
        return view('admin.session-detail', compact('session'));
    }

    public function students() {
        $students = User::where('is_admin', false)->latest()->get();
        return view('admin.students', compact('students'));
    }

    public function createStudent() {
        return view('admin.create-student');
    }

    public function storeStudent(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'is_admin' => false,
        ]);

        return redirect()->route('admin.students')->with('success', 'Student added successfully!');
    }

    public function deleteStudent($studentId) {
        $student = User::where('is_admin', false)->findOrFail($studentId);
        
        // Check if student has taken any exams
        if ($student->examSessions()->exists()) {
            return back()->with('error', 'Cannot delete student who has exam sessions.');
        }
        
        $student->delete();
        return back()->with('success', 'Student deleted successfully!');
    }

    public function deleteExam($examId) {
        $exam = Exam::findOrFail($examId);
        
        // Check if exam has been taken by students
        if ($exam->examSessions()->exists()) {
            return back()->with('error', 'Cannot delete exam that has been taken by students.');
        }
        
        $exam->delete();
        return back()->with('success', 'Exam deleted successfully!');
    }
}