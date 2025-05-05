<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserAnswerController;  // Thêm controller cho việc lưu câu trả lời

Route::get('/', fn () => view('welcome'));

// Routes cho xác thực người dùng
Auth::routes();

// Trang chủ sau khi login
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Trang hiển thị thông tin quiz và làm bài quiz
Route::get('quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');

// Admin routes
Route::middleware(['isAdmin:admin', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('quizzes', QuizController::class);
    Route::get('results/{result}', [ResultController::class, 'show'])->name('results.show');
    Route::resource('quizzes.questions', QuestionController::class);
    Route::resource('/forfun', QuestionController::class);
    Route::get('quizzes/{quiz}/questions/create', [QuestionController::class, 'create'])->name('quizzes.questions.create');
});

// Routes cho người dùng
Route::middleware(['auth'])->group(function () {
    // Trang bắt đầu làm quiz
    Route::get('quizzes/{quiz}/start', [QuizController::class, 'start'])->name('quizzes.start');
    // Trang kết quả sau khi người dùng làm xong quiz
    Route::get('quizzes/{quiz}/result', [ResultController::class, 'showResult'])->name('quizzes.result');
    // Gửi câu trả lời của người dùng
    Route::post('quizzes/{quiz}/submit', [QuizController::class, 'submitAnswers'])->name('quizzes.submit');
});
Route::middleware('auth')->get('/quiz/{quiz}/start', [QuizController::class, 'start'])->name('quiz.start');
Route::middleware('auth')->get('/quizzes/{quiz}/start', [QuizController::class, 'start'])->name('quizzes.start');
