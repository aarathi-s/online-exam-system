<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Student routes (logged in, not admin)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/result/{sessionId}', [StudentController::class, 'result'])->name('student.result');

    // Exam routes
    Route::get('/exam/{examId}/start', [ExamController::class, 'start'])->name('exam.start');
    Route::get('/exam/{sessionId}/question/{questionId}', [ExamController::class, 'showQuestion'])->name('exam.question');
    Route::post('/exam/{sessionId}/question/{questionId}/answer', [ExamController::class, 'saveAnswer'])->name('exam.answer');
    Route::post('/exam/{sessionId}/submit', [ExamController::class, 'submit'])->name('exam.submit');
    Route::post('/exam/{sessionId}/violation', [ExamController::class, 'recordViolation'])->name('exam.violation');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/exams', [AdminController::class, 'exams'])->name('exams');
    Route::get('/exams/create', [AdminController::class, 'createExam'])->name('exam.create');
    Route::post('/exams', [AdminController::class, 'storeExam'])->name('exam.store');
    Route::get('/exams/{examId}/questions', [AdminController::class, 'examQuestions'])->name('exam.questions');
    Route::post('/exams/{examId}/questions', [AdminController::class, 'storeQuestion'])->name('exam.question.store');
    Route::post('/exams/{examId}/toggle', [AdminController::class, 'toggleExam'])->name('exam.toggle');
    Route::get('/results', [AdminController::class, 'results'])->name('results');
    Route::get('/results/{sessionId}', [AdminController::class, 'sessionDetail'])->name('session.detail');

    // Student management routes
    Route::get('/students', [AdminController::class, 'students'])->name('students');
    Route::get('/students/create', [AdminController::class, 'createStudent'])->name('student.create');
    Route::post('/students', [AdminController::class, 'storeStudent'])->name('student.store');
    Route::delete('/students/{studentId}', [AdminController::class, 'deleteStudent'])->name('student.delete');

    // Exam management routes
    Route::delete('/exams/{examId}', [AdminController::class, 'deleteExam'])->name('exam.delete');
});


Route::get('/create-student', function () {
    $user = App\Models\User::firstOrCreate(
        ['email' => 'student1@student.com'],
        [
            'name' => 'Student One',
            'password' => 'student123',
            'is_admin' => false,
        ]
    );
    return 'Student created: ' . $user->email;
});

Route::get('/test-db', [ExamController::class, 'testDB']);

require __DIR__.'/auth.php';