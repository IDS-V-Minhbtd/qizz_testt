<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\UserAnswerController;

Route::get('/', fn () => view('welcome'));

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Route công khai
Route::get('quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');

// Admin routes
Route::middleware(['isAdmin:admin', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('quizzes', QuizController::class);
    Route::resource('quizzes.questions', QuestionController::class);
    Route::get('quizzes/{quiz}/questions/create', [QuestionController::class, 'create'])->name('quizzes.questions.create');
    Route::get('results/{result}', [ResultController::class, 'show'])->name('results.show');
});

// Người dùng đã đăng nhập
Route::middleware('auth')->group(function () {
    Route::post('quizz/{quiz}/submit', [UserAnswerController::class, 'submit'])->name('quizz.submit');
    Route::get('quizz/{quiz}/result', [UserAnswerController::class, 'resultByQuiz'])->name('quizz.result');
    Route::get('/quizz/{quiz}', [UserAnswerController::class, 'start'])->name('quizz.index');
});

