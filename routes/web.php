<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', fn () => view('welcome'));

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Admin routes
Route::middleware(['isAdmin:admin', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('quizzes', QuizController::class);
    Route::get('results/{result}', [ResultController::class, 'show'])->name('results.show');
    Route::resource('quizzes.questions', QuestionController::class);
});
